<?php

namespace App\Models;

use App\Models\Concerns\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SanteIntervention extends Model
{
    /** @use HasFactory<\Database\Factories\SanteInterventionFactory> */
    use HasFactory, LogsActivity;

    protected $table = 'sante_interventions';

    protected $fillable = [
        'lapin_id',
        'type',
        'libelle',
        'date_debut',
        'date_fin',
        'statut',
        'traitement_applique',
        'cout',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'date_debut' => 'date',
            'date_fin' => 'date',
            'cout' => 'decimal:2',
        ];
    }

    public function lapin(): BelongsTo
    {
        return $this->belongsTo(Lapin::class);
    }

    public function activityLogLabel(): string
    {
        return $this->libelle.' ('.($this->lapin?->identifiant ?? '#'.$this->lapin_id).')';
    }

    public const TYPES = [
        'maladie' => 'Maladie',
        'traitement' => 'Traitement',
        'vaccination' => 'Vaccination',
        'parasite_externe' => 'Parasite externe',
        'autre' => 'Autre',
    ];

    public const STATUTS = [
        'en_cours' => 'En cours',
        'gueri' => 'Guéri',
        'deces' => 'Décès',
    ];
}
