<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// Modèle représentant un partenaire (sponsor) associé à un événement spécifique
// Les partenaires sont liés à un seul événement via event_id (relation 1 → N)
// Ils sont créés/supprimés en même temps que l'événement auquel ils appartiennent
class Partner extends Model
{
    use HasFactory;

    // Champs autorisés à l'assignation de masse
    protected $fillable = [
        'event_id',
        'name',
        'logo',
        'website',
        'email',
        'phone',
    ];

    // Relation : un partenaire appartient à un seul événement
    // La suppression en cascade est gérée au niveau de la migration (onDelete cascade)
    public function event()
    {
        return $this->belongsTo(Event::class);
    }
}
