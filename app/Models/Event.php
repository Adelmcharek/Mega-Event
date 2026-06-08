<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// Modèle représentant un événement de la plateforme
// Cycle de vie du statut : draft → pending → published | rejected
//   draft     : brouillon non soumis par l'organisateur
//   pending   : soumis, en attente de validation par l'administrateur
//   published : approuvé et visible publiquement
//   rejected  : refusé par l'administrateur (motif stocké dans rejection_reason)
class Event extends Model
{
    use HasFactory;

    // Champs autorisés à l'assignation de masse
    protected $fillable = [
        'user_id',
        'title',
        'description',
        'category',
        'date',
        'location',
        'lat',       // Latitude pour l'affichage sur carte (Leaflet / OpenStreetMap)
        'lng',       // Longitude pour l'affichage sur carte
        'capacity',
        'price',
        'cover_image',
        'status',
        'visibility',
        'rejection_reason',
    ];

    // Conversion automatique : date en objet Carbon, price en nombre décimal à 2 chiffres
    protected function casts(): array
    {
        return [
            'date'  => 'datetime',
            'price' => 'decimal:2',
        ];
    }

    // ═══════════════════════════════════════════
    // RELATIONS
    // ═══════════════════════════════════════════

    // Relation : un événement appartient à un organisateur (clé étrangère user_id)
    public function organiser()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Relation : un événement peut avoir plusieurs inscriptions (1 → N)
    public function registrations()
    {
        return $this->hasMany(Registration::class);
    }

    // Relation Many-to-Many : participants inscrits via la table pivot 'registrations'
    public function participants()
    {
        return $this->belongsToMany(User::class, 'registrations')
                    ->withPivot('status', 'registered_at')
                    ->withTimestamps();
    }

    // Relation : un événement peut avoir plusieurs partenaires (sponsors)
    public function partners()
    {
        return $this->hasMany(Partner::class);
    }

    // ═══════════════════════════════════════════
    // MÉTHODES MÉTIER
    // ═══════════════════════════════════════════

    // Vérifie si l'événement est complet (inscriptions >= capacité maximale)
    public function isFull(): bool
    {
        return $this->registrations()->count() >= $this->capacity;
    }

    // Retourne le nombre de places restantes (minimum 0 pour éviter les valeurs négatives)
    public function spotsLeft(): int
    {
        return max(0, $this->capacity - $this->registrations()->count());
    }

    // Calcule le taux de remplissage en pourcentage (ex: 75.5%)
    // Retourne 0 si la capacité est nulle pour éviter une division par zéro
    public function fillRate(): float
    {
        if ($this->capacity === 0) return 0;
        return round(($this->registrations()->count() / $this->capacity) * 100, 1);
    }

    const EUR_TO_TND = 3.3;

    public function getPriceInTnd(): float
    {
        return round($this->price * self::EUR_TO_TND, 2);
    }

    public function getPriceInEur(): float
    {
        return $this->price;
    }
}
