from flask import Flask, jsonify, request
import pandas as pd
import numpy as np
from sklearn.metrics.pairwise import cosine_similarity
import sqlite3
import os

app = Flask(__name__)

DB_PATH = '/home/adelm/mega_event/database/database.sqlite'

def get_recommendations(user_id, n_recommendations=3):
    try:
        conn = sqlite3.connect(DB_PATH)
        
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
        
        similarity_matrix = cosine_similarity(user_event_matrix)
        similarity_df = pd.DataFrame(
            similarity_matrix,
            index=user_event_matrix.index,
            columns=user_event_matrix.index
        )
        
        similar_users = similarity_df[user_id].drop(user_id).sort_values(ascending=False)
        
        if len(similar_users) == 0:
            return fallback_recommendations(available_events)
        
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
    if len(available_events) == 0:
        return []
    popular = available_events.sort_values('registered_count', ascending=False).head(3).to_dict('records')
    for event in popular:
        event['score'] = 0
        event['reason'] = 'popularity'
    return popular

@app.route('/recommend/<int:user_id>', methods=['GET'])
def recommend(user_id):
    n = request.args.get('n', 3, type=int)
    recommendations = get_recommendations(user_id, n)
    return jsonify({
        'user_id': user_id,
        'recommendations': recommendations,
        'count': len(recommendations)
    })

@app.route('/health', methods=['GET'])
def health():
    return jsonify({'status': 'ok', 'service': 'MEGA EVENT Recommender'})

if __name__ == '__main__':
    app.run(host='0.0.0.0', port=5000, debug=True)
