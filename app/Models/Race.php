<?php

namespace App\Models;

use App\Models\Concerns\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Race extends Model
{
    /** @use HasFactory<\Database\Factories\RaceFactory> */
    use HasFactory, SoftDeletes, LogsActivity;

    protected $fillable = [
        'nom',
        'categorie',
        'poids_min_kg',
        'poids_max_kg',
        'description',
    ];

    protected function casts(): array
    {
        return [
            'poids_min_kg' => 'decimal:2',
            'poids_max_kg' => 'decimal:2',
        ];
    }

    public function lapins(): HasMany
    {
        return $this->hasMany(Lapin::class);
    }

    public function activityLogLabel(): string
    {
        return $this->nom;
    }

    public const CATEGORIES = [
        'naine' => 'Naine (jusqu\'à 1,5 kg)',
        'legere' => 'Légère (2 à 3 kg)',
        'moyenne' => 'Moyenne (3 à 5 kg)',
        'lourde' => 'Lourde (plus de 5 kg)',
    ];
}
