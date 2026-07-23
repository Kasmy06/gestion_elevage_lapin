<?php

namespace App\Models;

use App\Models\Concerns\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cage extends Model
{
    /** @use HasFactory<\Database\Factories\CageFactory> */
    use HasFactory, SoftDeletes, LogsActivity;

    protected $fillable = [
        'numero',
        'emplacement',
        'type',
        'capacite',
        'notes',
    ];

    public function lapins(): HasMany
    {
        return $this->hasMany(Lapin::class);
    }

    public function mouvementsAliment(): HasMany
    {
        return $this->hasMany(MouvementAliment::class);
    }

    public function getOccupationAttribute(): int
    {
        return $this->lapins()->count();
    }

    public function getPlacesDisponiblesAttribute(): int
    {
        return max(0, $this->capacite - $this->occupation);
    }

    public function estLibre(): bool
    {
        return $this->places_disponibles > 0;
    }

    public function activityLogLabel(): string
    {
        return $this->numero;
    }

    public const TYPES = [
        'individuelle' => 'Individuelle',
        'maternite' => 'Maternité',
        'engraissement' => 'Engraissement',
        'quarantaine' => 'Quarantaine',
    ];
}
