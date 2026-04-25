<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Migration : création de la table des événements
// Table des événements — cœur de la plateforme MEGA EVENT
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();

            // Clé étrangère vers l'organisateur — suppression en cascade si l'utilisateur est supprimé
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            $table->string('title');
            $table->text('description')->nullable();

            // Catégorie de l'événement : liste fermée pour faciliter le filtrage et les statistiques
            $table->enum('category', ['professional', 'cultural', 'corporate'])->default('professional');

            $table->dateTime('date');
            $table->string('location');
            $table->integer('capacity');

            // Prix en euros avec 2 décimales — 0 pour les événements gratuits
            $table->decimal('price', 8, 2)->default(0);

            $table->string('cover_image')->nullable();

            // Cycle de vie du statut :
            //   draft     → brouillon non soumis
            //   pending   → soumis, en attente de validation admin
            //   published → approuvé, visible publiquement
            //   rejected  → refusé par l'administrateur
            $table->enum('status', ['draft', 'pending', 'published', 'rejected'])->default('draft');

            // visibility : 'public' visible dans le catalogue, 'private' accessible uniquement par lien direct
            $table->enum('visibility', ['public', 'private'])->default('public');

            // rejection_reason : motif du rejet saisi par l'administrateur, affiché à l'organisateur
            $table->text('rejection_reason')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
