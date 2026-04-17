<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Partner extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'logo',
        'website',
        'email',
        'phone',
        'description',
    ];

    // Relationships
    public function events()
    {
        return $this->belongsToMany(Event::class, 'event_partner')
                    ->withTimestamps();
    }
}