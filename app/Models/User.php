<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

// Modèle principal représentant un utilisateur de la plateforme
// Implémente MustVerifyEmail pour forcer la vérification de l'adresse email avant accès
class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable;

    // Champs autorisés à l'assignation de masse
    // role : détermine les droits d'accès (admin | organisateur | participant)
    // status : permet de désactiver un compte sans le supprimer (active | inactive)
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'status',
        'avatar',
        'phone',
    ];

    // Champs exclus de la sérialisation JSON pour des raisons de sécurité
    protected $hidden = [
        'password',
        'remember_token',
    ];

    // Conversion automatique des types : date ISO pour email_verified_at, hachage bcrypt pour password
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
        ];
    }

    // ═══════════════════════════════════════════
    // RELATIONS
    // ═══════════════════════════════════════════

    // Relation : un organisateur peut créer plusieurs événements (1 → N)
    public function events()
    {
        return $this->hasMany(Event::class);
    }

    // Relation : un participant peut avoir plusieurs inscriptions (1 → N)
    public function registrations()
    {
        return $this->hasMany(Registration::class);
    }

    // Relation Many-to-Many via la table pivot 'registrations'
    // Inclut les colonnes pivot : status et registered_at
    public function registeredEvents()
    {
        return $this->belongsToMany(Event::class, 'registrations')
                    ->withPivot('status', 'registered_at')
                    ->withTimestamps();
    }

    // ═══════════════════════════════════════════
    // MÉTHODES UTILITAIRES — Vérification du rôle
    // ═══════════════════════════════════════════

    // Vérifie si l'utilisateur connecté est un administrateur
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    // Vérifie si l'utilisateur connecté est un organisateur
    public function isOrganisateur(): bool
    {
        return $this->role === 'organisateur';
    }

    // Vérifie si l'utilisateur connecté est un participant
    public function isParticipant(): bool
    {
        return $this->role === 'participant';
    }
}
