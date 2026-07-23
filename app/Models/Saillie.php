<?php

namespace App\Models;

use App\Models\Concerns\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Saillie extends Model
{
    /** @use HasFactory<\Database\Factories\SaillieFactory> */
    use HasFactory, LogsActivity;

    public const JOURS_AVANT_DIAGNOSTIC = 10;

    public const JOURS_GESTATION = 31;

    protected $fillable = [
        'male_id',
        'femelle_id',
        'date_saillie',
        'diagnostic_gestation',
        'date_diagnostic',
        'date_mise_bas_prevue',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'date_saillie' => 'date',
            'date_diagnostic' => 'date',
            'date_mise_bas_prevue' => 'date',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Saillie $saillie) {
            if (! $saillie->date_mise_bas_prevue && $saillie->date_saillie) {
                $saillie->date_mise_bas_prevue = $saillie->date_saillie->copy()->addDays(self::JOURS_GESTATION);
            }
        });
    }

    public function male(): BelongsTo
    {
        return $this->belongsTo(Lapin::class, 'male_id');
    }

    public function femelle(): BelongsTo
    {
        return $this->belongsTo(Lapin::class, 'femelle_id');
    }

    public function miseBas(): HasOne
    {
        return $this->hasOne(MiseBas::class);
    }

    public function diagnosticPossibleLe(): \Illuminate\Support\Carbon
    {
        return $this->date_saillie->copy()->addDays(self::JOURS_AVANT_DIAGNOSTIC);
    }

    public function diagnosticEnAttente(): bool
    {
        return $this->diagnostic_gestation === 'en_attente'
            && now()->greaterThanOrEqualTo($this->diagnosticPossibleLe());
    }

    public function enAttenteMiseBas(): bool
    {
        return $this->diagnostic_gestation === 'positif' && ! $this->miseBas()->exists();
    }

    public function activityLogLabel(): string
    {
        return ($this->femelle?->identifiant ?? '#'.$this->femelle_id).' × '.($this->male?->identifiant ?? '#'.$this->male_id);
    }

    public const DIAGNOSTICS = [
        'en_attente' => 'En attente',
        'positif' => 'Positif (gestante)',
        'negatif' => 'Négatif',
    ];
}
