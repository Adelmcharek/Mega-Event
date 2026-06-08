# =============================================================================
# MEGA EVENT - Microservice de recommandation d'événements
# =============================================================================
# Ce fichier est un microservice indépendant basé sur le filtrage collaboratif.
# Le principe : si deux utilisateurs ont assisté aux mêmes événements,
# ils ont des goûts similaires, donc on peut recommander à l'un
# les événements que l'autre a aimés — sans analyser le contenu des événements.
# Ce service tourne séparément de Laravel et répond via une API REST (Flask).
# =============================================================================

# Flask : micro-framework Python pour créer des routes HTTP (API REST)
from flask import Flask, jsonify, request
# pandas : manipulation de tableaux de données (comme Excel en Python)
import pandas as pd
# numpy : calculs mathématiques sur des tableaux de nombres
import numpy as np
# cosine_similarity : fonction qui mesure la ressemblance entre deux vecteurs
from sklearn.metrics.pairwise import cosine_similarity
# sqlite3 : module intégré à Python pour lire la base de données SQLite
import sqlite3
# os : utilitaires système (accès aux variables d'environnement, chemins de fichiers)
import os

app = Flask(__name__)

DB_PATH = '/home/adelm/mega_event/database/database.sqlite'

def get_recommendations(user_id, n_recommendations=3):
    try:
        conn = sqlite3.connect(DB_PATH)

        # On récupère uniquement les inscriptions confirmées à des événements publiés.
        # Ces données représentent les "votes" implicites des utilisateurs :
        # s'inscrire à un événement signifie qu'on s'y intéresse.
        registrations_query = """
            SELECT r.user_id, r.event_id, e.category
            FROM registrations r
            JOIN events e ON r.event_id = e.id
            WHERE r.status = 'confirmed'
            AND e.status = 'published'
        """
        registrations = pd.read_sql_query(registrations_query, conn)

        events_query = """
            SELECT id, title, category, date, location, capacity, price,
                   (SELECT COUNT(*) FROM registrations
                    WHERE event_id = events.id
                    AND status = 'confirmed') as registered_count
            FROM events
            WHERE status = 'published'
            AND date > datetime('now')
            AND id NOT IN (
                SELECT event_id FROM registrations
                WHERE user_id = ? AND status = 'confirmed'
            )
        """
        available_events = pd.read_sql_query(events_query, conn, params=[user_id])
        conn.close()

        if len(registrations) < 2 or len(available_events) == 0:
            return fallback_recommendations(available_events)

        # On construit une matrice utilisateur-événement :
        # chaque ligne = un utilisateur, chaque colonne = un événement.
        # La valeur vaut 1 si l'utilisateur est inscrit à cet événement, 0 sinon.
        # Cette matrice est la base de tout le filtrage collaboratif.
        registrations['registered'] = 1
        user_event_matrix = registrations.pivot_table(
         index='user_id',
         columns='event_id',
         values='registered',
         aggfunc='max',
         fill_value=0
)       

        if user_id not in user_event_matrix.index:
            return fallback_recommendations(available_events)

        # La similarité cosinus compare les vecteurs d'inscriptions de chaque paire d'utilisateurs.
        # Le résultat est compris entre 0 et 1 : 0 = rien en commun, 1 = goûts identiques.
        # Exemple : si Alice et Bob ont tous les deux assisté aux mêmes 3 événements,
        # leur similarité sera proche de 1.
        similarity_matrix = cosine_similarity(user_event_matrix)
        similarity_df = pd.DataFrame(
            similarity_matrix,
            index=user_event_matrix.index,
            columns=user_event_matrix.index
        )

        similar_users = similarity_df[user_id].drop(user_id).sort_values(ascending=False)

        if len(similar_users) == 0:
            return fallback_recommendations(available_events)

        # On identifie les 5 utilisateurs les plus similaires à l'utilisateur courant,
        # puis on récupère les événements auxquels ils se sont inscrits.
        # Chaque événement reçoit un score : plus l'utilisateur similaire lui ressemble,
        # plus le score de l'événement est élevé — ce sont nos recommandations personnalisées.
        top_similar_users = similar_users.head(5).index.tolist()
        similar_registrations = registrations[
            registrations['user_id'].isin(top_similar_users)
        ]

        event_scores = {}
        for _, row in similar_registrations.iterrows():
            event_id = row['event_id']
            similar_user = row['user_id']
            score = similar_users[similar_user]
            event_scores[event_id] = event_scores.get(event_id, 0) + score

        available_ids = available_events['id'].tolist()
        filtered_scores = {eid: s for eid, s in event_scores.items() if eid in available_ids}

        if not filtered_scores:
            return fallback_recommendations(available_events)

        recommended_ids = sorted(filtered_scores, key=filtered_scores.get, reverse=True)[:n_recommendations]
        recommended = available_events[available_events['id'].isin(recommended_ids)].to_dict('records')

        for event in recommended:
            event['score'] = round(filtered_scores.get(event['id'], 0), 3)
            event['reason'] = 'collaborative_filtering'

        return recommended

    except Exception as e:
        print(f"Erreur: {e}")
        return []

def fallback_recommendations(available_events):
    # Mécanisme de secours (fallback) : si l'utilisateur est nouveau ou si les données
    # sont insuffisantes pour calculer une similarité, on ne peut pas faire du filtrage collaboratif.
    # Dans ce cas, on recommande simplement les événements les plus populaires,
    # c'est-à-dire ceux qui ont le plus grand nombre d'inscrits confirmés.
    if len(available_events) == 0:
        return []
    popular = available_events.sort_values('registered_count', ascending=False).head(3).to_dict('records')
    for event in popular:
        event['score'] = 0
        event['reason'] = 'popularity'
    return popular

# Endpoint principal du service : reçoit l'identifiant d'un utilisateur dans l'URL
# et retourne une liste de recommandations personnalisées au format JSON.
# Exemple d'appel : GET /recommend/42?n=5 → recommandations pour l'utilisateur 42
@app.route('/recommend/<int:user_id>', methods=['GET'])
def recommend(user_id):
    n = request.args.get('n', 3, type=int)
    recommendations = get_recommendations(user_id, n)
    return jsonify({
        'user_id': user_id,
        'recommendations': recommendations,
        'count': len(recommendations)
    })

# Endpoint de vérification (health check) : permet de savoir si le service est bien démarré.
# Laravel appelle cette route avant d'envoyer des requêtes de recommandation.
# Retourne simplement {"status": "ok"} si le service tourne correctement.
@app.route('/health', methods=['GET'])
def health():
    return jsonify({'status': 'ok', 'service': 'MEGA EVENT Recommender'})

if __name__ == '__main__':
    app.run(host='0.0.0.0', port=5000, debug=True)
