<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'category',
        'date',
        'location',
        'capacity',
        'price',
        'cover_image',
        'status',
        'visibility',
        'rejection_reason',
    ];

    protected function casts(): array
    {
        return [
            'date'  => 'datetime',
            'price' => 'decimal:2',
        ];
    }

    // Relationships
    public function organiser()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function registrations()
    {
        return $this->hasMany(Registration::class);
    }

    public function participants()
    {
        return $this->belongsToMany(User::class, 'registrations')
                    ->withPivot('status', 'registered_at')
                    ->withTimestamps();
    }

    public function partners()
    {
        return $this->belongsToMany(Partner::class, 'event_partner')
                    ->withTimestamps();
    }

    // Helpers
    public function isFull(): bool
    {
        return $this->registrations()->count() >= $this->capacity;
    }

    public function spotsLeft(): int
    {
        return max(0, $this->capacity - $this->registrations()->count());
    }

    public function fillRate(): float
    {
        if ($this->capacity === 0) return 0;
        return round(($this->registrations()->count() / $this->capacity) * 100, 1);
    }
}