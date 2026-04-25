<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Migration : création de la table des inscriptions aux événements
// Table pivot entre 'users' et 'events' avec des métadonnées supplémentaires
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('registrations', function (Blueprint $table) {
            $table->id();

            // Clé étrangère vers l'utilisateur inscrit — suppression en cascade
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            // Clé étrangère vers l'événement — suppression en cascade si l'événement est supprimé
            $table->foreignId('event_id')->constrained()->onDelete('cascade');

            // Statut de l'inscription :
            //   pending   → en attente de confirmation (non utilisé actuellement)
            //   confirmed → inscription validée et active
            //   cancelled → annulée par le participant (la place est libérée)
            $table->enum('status', ['pending', 'confirmed', 'cancelled'])->default('confirmed');

            // Horodatage de l'inscription — distinct de created_at pour un suivi précis
            $table->timestamp('registered_at')->useCurrent();

            $table->timestamps();

            // Contrainte UNIQUE sur (user_id, event_id) pour éviter les doublons d'inscription
            // Un participant ne peut s'inscrire qu'une seule fois par événement
            // La vérification en PHP (RegistrationController) complète cette contrainte
            $table->unique(['user_id', 'event_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('registrations');
    }
};
