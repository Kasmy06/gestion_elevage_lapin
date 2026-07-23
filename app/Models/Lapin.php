<?php

namespace App\Models;

use App\Models\Concerns\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Lapin extends Model
{
    /** @use HasFactory<\Database\Factories\LapinFactory> */
    use HasFactory, SoftDeletes, LogsActivity;

    protected $fillable = [
        'identifiant',
        'race_id',
        'sexe',
        'date_naissance',
        'pere_id',
        'mere_id',
        'cage_id',
        'statut',
        'origine',
        'poids_actuel_g',
        'date_acquisition',
        'photo_path',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'date_naissance' => 'date',
            'date_acquisition' => 'date',
        ];
    }

    public function race(): BelongsTo
    {
        return $this->belongsTo(Race::class);
    }

    public function cage(): BelongsTo
    {
        return $this->belongsTo(Cage::class);
    }

    public function pere(): BelongsTo
    {
        return $this->belongsTo(Lapin::class, 'pere_id');
    }

    public function mere(): BelongsTo
    {
        return $this->belongsTo(Lapin::class, 'mere_id');
    }

    public function descendantsPaternels(): HasMany
    {
        return $this->hasMany(Lapin::class, 'pere_id');
    }

    public function descendantsMaternels(): HasMany
    {
        return $this->hasMany(Lapin::class, 'mere_id');
    }

    public function saillesMale(): HasMany
    {
        return $this->hasMany(Saillie::class, 'male_id');
    }

    public function saillesFemelle(): HasMany
    {
        return $this->hasMany(Saillie::class, 'femelle_id');
    }

    public function pesees(): HasMany
    {
        return $this->hasMany(Pesee::class)->orderBy('date_pesee');
    }

    public function santeInterventions(): HasMany
    {
        return $this->hasMany(SanteIntervention::class)->orderByDesc('date_debut');
    }

    public function sorties(): HasMany
    {
        return $this->hasMany(Sortie::class);
    }

    public function getAgeEnJoursAttribute(): ?int
    {
        return $this->date_naissance?->diffInDays(now());
    }

    public function estActif(): bool
    {
        return ! in_array($this->statut, ['vendu', 'abattu', 'mort', 'donne'], true);
    }

    public function getAgeLisibleAttribute(): ?string
    {
        $jours = $this->age_en_jours;

        if ($jours === null) {
            return null;
        }

        if ($jours < 30) {
            return $jours.' j';
        }

        if ($jours < 365) {
            return intdiv($jours, 30).' mois';
        }

        $annees = intdiv($jours, 365);
        $moisRestants = intdiv($jours % 365, 30);

        return $moisRestants > 0
            ? $annees.' an'.($annees > 1 ? 's' : '').' '.$moisRestants.' mois'
            : $annees.' an'.($annees > 1 ? 's' : '');
    }

    public function statutCouleur(): string
    {
        return match ($this->statut) {
            'jeune' => 'blue',
            'reproducteur' => 'green',
            'engraissement' => 'yellow',
            'vendu' => 'purple',
            'abattu' => 'gray',
            'mort' => 'red',
            'donne' => 'indigo',
            default => 'gray',
        };
    }

    public function scopeDisponiblesPourSaillie($query, string $sexe)
    {
        return $query->where('sexe', $sexe)
            ->where('statut', 'reproducteur');
    }

    public function activityLogLabel(): string
    {
        return $this->identifiant;
    }

    public const STATUTS = [
        'jeune' => 'Jeune',
        'reproducteur' => 'Reproducteur',
        'engraissement' => 'Engraissement',
        'vendu' => 'Vendu',
        'abattu' => 'Abattu',
        'mort' => 'Mort',
        'donne' => 'Donné',
    ];

    public const SEXES = [
        'male' => 'Mâle',
        'femelle' => 'Femelle',
    ];

    public const ORIGINES = [
        'naissance_elevage' => 'Naissance à l\'élevage',
        'achat' => 'Achat',
    ];
}
