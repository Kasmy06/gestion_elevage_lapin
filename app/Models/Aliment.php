<?php

namespace App\Models;

use App\Models\Concerns\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Aliment extends Model
{
    /** @use HasFactory<\Database\Factories\AlimentFactory> */
    use HasFactory, SoftDeletes, LogsActivity;

    protected $fillable = [
        'nom',
        'type',
        'unite',
        'stock_actuel',
        'seuil_alerte',
    ];

    protected function casts(): array
    {
        return [
            'stock_actuel' => 'decimal:2',
            'seuil_alerte' => 'decimal:2',
        ];
    }

    public function mouvements(): HasMany
    {
        return $this->hasMany(MouvementAliment::class);
    }

    public function stockBas(): bool
    {
        return $this->seuil_alerte !== null && $this->stock_actuel <= $this->seuil_alerte;
    }

    public function activityLogLabel(): string
    {
        return $this->nom;
    }

    public const TYPES = [
        'fourrage' => 'Fourrage vert',
        'concentre' => 'Concentré',
        'mineraux' => 'Minéraux / sel',
        'autre' => 'Autre',
    ];
}
