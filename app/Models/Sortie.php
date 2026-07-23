<?php

namespace App\Models;

use App\Models\Concerns\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Sortie extends Model
{
    /** @use HasFactory<\Database\Factories\SortieFactory> */
    use HasFactory, LogsActivity;

    protected $fillable = [
        'lapin_id',
        'type',
        'date',
        'poids_g',
        'prix',
        'acheteur',
        'cause',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'prix' => 'decimal:2',
        ];
    }

    public function lapin(): BelongsTo
    {
        return $this->belongsTo(Lapin::class);
    }

    public function activityLogLabel(): string
    {
        return (self::TYPES[$this->type] ?? $this->type).' — '.($this->lapin?->identifiant ?? '#'.$this->lapin_id);
    }

    /**
     * Statut du lapin correspondant à chaque type de sortie.
     */
    public const STATUT_LAPIN = [
        'vente' => 'vendu',
        'abattage' => 'abattu',
        'mort' => 'mort',
        'don' => 'donne',
    ];

    public const TYPES = [
        'vente' => 'Vente',
        'abattage' => 'Abattage',
        'mort' => 'Mortalité',
        'don' => 'Don',
    ];
}
