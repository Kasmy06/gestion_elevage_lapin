<?php

namespace App\Models;

use App\Models\Concerns\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MouvementAliment extends Model
{
    /** @use HasFactory<\Database\Factories\MouvementAlimentFactory> */
    use HasFactory, LogsActivity;

    protected $table = 'mouvements_aliment';

    protected $fillable = [
        'aliment_id',
        'cage_id',
        'date',
        'type_mouvement',
        'quantite',
        'cout',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'quantite' => 'decimal:2',
            'cout' => 'decimal:2',
        ];
    }

    public function aliment(): BelongsTo
    {
        return $this->belongsTo(Aliment::class);
    }

    public function cage(): BelongsTo
    {
        return $this->belongsTo(Cage::class);
    }

    public function activityLogLabel(): string
    {
        return trim((self::TYPES_MOUVEMENT[$this->type_mouvement] ?? $this->type_mouvement).' — '.($this->aliment?->nom ?? '#'.$this->aliment_id));
    }

    public const TYPES_MOUVEMENT = [
        'entree' => 'Entrée en stock',
        'distribution' => 'Distribution',
    ];
}
