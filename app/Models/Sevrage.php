<?php

namespace App\Models;

use App\Models\Concerns\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Collection;

class Sevrage extends Model
{
    /** @use HasFactory<\Database\Factories\SevrageFactory> */
    use HasFactory, LogsActivity;

    protected $fillable = [
        'mise_bas_id',
        'date_sevrage',
        'nb_sevres',
        'poids_moyen_g',
        'lapereaux_generes',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'date_sevrage' => 'date',
            'lapereaux_generes' => 'boolean',
        ];
    }

    public function miseBas(): BelongsTo
    {
        return $this->belongsTo(MiseBas::class);
    }

    public function activityLogLabel(): string
    {
        $femelle = $this->miseBas?->femelle?->identifiant;

        return 'Sevrage'.($femelle ? ' de '.$femelle : ' #'.$this->id);
    }

    /**
     * Lapereaux sevrés encore vivants, non encore identifiés (sans fiche Lapin).
     */
    public function nbLapereauxAIdentifier(): int
    {
        return max(0, $this->nb_sevres - $this->miseBas->nbMortsAuStade(MortaliteLapereaux::STADE_SEVRAGE));
    }

    /**
     * Génère individuellement les lapereaux sevrés comme nouvelles fiches Lapin
     * (identification, chapitre 9 de l'Agrodok), avec généalogie et cage
     * héritées de la portée. Idempotent : ne génère qu'une seule fois.
     */
    public function genererLapereaux(?int $cageId = null): Collection
    {
        if ($this->lapereaux_generes) {
            return collect();
        }

        $miseBas = $this->miseBas()->with('saillie')->first();
        $saillie = $miseBas->saillie;

        $lapereaux = collect(range(1, $this->nbLapereauxAIdentifier()))->map(function (int $index) use ($miseBas, $saillie, $cageId) {
            return Lapin::create([
                'identifiant' => sprintf('P%d-%d', $miseBas->id, $index),
                'race_id' => $saillie->femelle?->race_id,
                'sexe' => null,
                'date_naissance' => $miseBas->date_mise_bas,
                'pere_id' => $saillie->male_id,
                'mere_id' => $saillie->femelle_id,
                'cage_id' => $cageId,
                'statut' => 'jeune',
                'origine' => 'naissance_elevage',
                'poids_actuel_g' => $this->poids_moyen_g,
            ]);
        });

        $this->update(['lapereaux_generes' => true]);

        return $lapereaux;
    }
}
