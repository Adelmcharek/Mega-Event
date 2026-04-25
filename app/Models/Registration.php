<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// Modèle représentant une inscription d'un participant à un événement
// La table possède une contrainte UNIQUE sur (user_id, event_id)
// pour garantir qu'un même utilisateur ne peut s'inscrire qu'une seule fois par événement
// status : pending | confirmed | cancelled
class Registration extends Model
{
    use HasFactory;

    // Champs autorisés à l'assignation de masse
    protected $fillable = [
        'user_id',
        'event_id',
        'status',
        'registered_at',
    ];

    // Conversion automatique de registered_at en objet Carbon
    protected function casts(): array
    {
        return [
            'registered_at' => 'datetime',
        ];
    }

    // ═══════════════════════════════════════════
    // RELATIONS
    // ═══════════════════════════════════════════

    // Relation : une inscription appartient à un utilisateur (participant)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relation : une inscription est liée à un événement
    public function event()
    {
        return $this->belongsTo(Event::class);
    }
}
