<?php

namespace App\Models;

use App\Models\Concerns\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Vente extends Model
{
    /** @use HasFactory<\Database\Factories\VenteFactory> */
    use HasFactory, SoftDeletes, LogsActivity;

    protected $fillable = [
        'client_id',
        'description',
        'quantite',
        'prix_unitaire',
        'montant_total',
        'date',
        'mode_paiement',
        'statut_paiement',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'quantite' => 'decimal:2',
            'prix_unitaire' => 'decimal:2',
            'montant_total' => 'decimal:2',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (Vente $vente) {
            $vente->montant_total = $vente->quantite * $vente->prix_unitaire;
        });
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function activityLogLabel(): string
    {
        return $this->description;
    }

    public const MODES_PAIEMENT = [
        'especes' => 'Espèces',
        'mobile_money' => 'Mobile Money',
        'virement' => 'Virement',
        'credit' => 'Crédit',
    ];

    public const STATUTS_PAIEMENT = [
        'paye' => 'Payé',
        'partiel' => 'Partiel',
        'impaye' => 'Impayé',
    ];
}
