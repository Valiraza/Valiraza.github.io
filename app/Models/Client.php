<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Client extends Model
{
    protected $fillable = [
        'nom',
        'prenom',
        'email',
        'tel',
        'date',
        'statut',
        'points',
        'niveau',
        'depenses',
    ];

    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class, 'email', 'email');
    }
}
