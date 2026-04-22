<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Reservation extends Model
{
    public const STATUS_PENDING = 'en_attente';
    public const STATUS_ACCEPTED = 'acceptee';
    public const STATUS_REFUSED = 'refusee';

    protected $fillable = [
        'circuit_id',
        'circuit_nom',
        'statut',
        'nom',
        'email',
        'telephone',
        'voyageurs',
        'places',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function circuit(): BelongsTo
    {
        return $this->belongsTo(Circuit::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class, 'email', 'email');
    }
}
