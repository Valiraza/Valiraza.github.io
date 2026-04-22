<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Circuit extends Model
{
    public const TYPES = [
        'Randonnee & Trek',
        'Classe Verte',
        'Team Building',
        'Circuit Sejour',
    ];

    protected $fillable = [
        'nom',
        'slug',
        'destination',
        'depart',
        'retour',
        'prix',
        'places',
        'description',
        'programme',
        'detail',
        'statut',
        'image',
        'type',
    ];

    protected $appends = [
        'image_url',
    ];

    protected $casts = [
        'depart' => 'date',
        'retour' => 'date',
        'prix' => 'decimal:2',
        'programme' => 'array',
        'detail' => 'array',
    ];

    protected static function booted(): void
    {
        static::saving(function (self $circuit): void {
            $baseSlug = $circuit->slug ?: Str::slug($circuit->nom);

            if (!$baseSlug) {
                $baseSlug = 'circuit';
            }

            $slug = $baseSlug;
            $suffix = 2;

            while (
                static::query()
                    ->where('slug', $slug)
                    ->when($circuit->exists, fn ($query) => $query->whereKeyNot($circuit->getKey()))
                    ->exists()
            ) {
                $slug = "{$baseSlug}-{$suffix}";
                $suffix++;
            }

            $circuit->slug = $slug;
        });
    }

    public function getImageUrlAttribute(): ?string
    {
        if (!$this->image) {
            return null;
        }

        if (str_starts_with($this->image, 'http://') || str_starts_with($this->image, 'https://') || str_starts_with($this->image, '/')) {
            return $this->image;
        }

        return '/storage/' . ltrim($this->image, '/');
    }

    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }
}
