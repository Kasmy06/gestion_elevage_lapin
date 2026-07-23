<?php

namespace App\Models;

use App\Models\Concerns\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class MiseBas extends Model
{
    /** @use HasFactory<\Database\Factories\MiseBasFactory> */
    use HasFactory, LogsActivity;

    protected $table = 'mises_bas';

    public const JOURS_AVANT_SEVRAGE = 35;

    protected $fillable = [
        'saillie_id',
        'femelle_id',
        'date_mise_bas',
        'nb_nes_vivants',
        'nb_morts_nes',
        'date_sevrage_prevue',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'date_mise_bas' => 'date',
            'date_sevrage_prevue' => 'date',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (MiseBas $miseBas) {
            if (! $miseBas->date_sevrage_prevue && $miseBas->date_mise_bas) {
                $miseBas->date_sevrage_prevue = $miseBas->date_mise_bas->copy()->addDays(self::JOURS_AVANT_SEVRAGE);
            }
        });
    }

    public function saillie(): BelongsTo
    {
        return $this->belongsTo(Saillie::class);
    }

    public function femelle(): BelongsTo
    {
        return $this->belongsTo(Lapin::class, 'femelle_id');
    }

    public function sevrage(): HasOne
    {
        return $this->hasOne(Sevrage::class);
    }

    public function enAttenteSevrage(): bool
    {
        return ! $this->sevrage()->exists();
    }

    public function activityLogLabel(): string
    {
        return 'Mise bas de '.($this->femelle?->identifiant ?? '#'.$this->femelle_id);
    }
}
