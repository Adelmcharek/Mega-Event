<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'status',
        'avatar',
        'phone',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
        ];
    }

    // Relationships
    public function events()
    {
        return $this->hasMany(Event::class);
    }

    public function registrations()
    {
        return $this->hasMany(Registration::class);
    }

    public function registeredEvents()
    {
        return $this->belongsToMany(Event::class, 'registrations')
                    ->withPivot('status', 'registered_at')
                    ->withTimestamps();
    }

    // Role helpers
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isOrganisateur(): bool
    {
        return $this->role === 'organisateur';
    }

    public function isParticipant(): bool
    {
        return $this->role === 'participant';
    }
}