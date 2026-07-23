<?php

namespace App\Models;

use App\Models\Concerns\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Depense extends Model
{
    /** @use HasFactory<\Database\Factories\DepenseFactory> */
    use HasFactory, SoftDeletes, LogsActivity;

    protected $fillable = [
        'categorie',
        'libelle',
        'montant',
        'date',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'montant' => 'decimal:2',
        ];
    }

    public function activityLogLabel(): string
    {
        return $this->libelle;
    }

    public const CATEGORIES = [
        'alimentation' => 'Alimentation',
        'sante' => 'Santé',
        'equipement' => 'Équipement',
        'main_oeuvre' => "Main d'œuvre",
        'transport' => 'Transport',
        'autre' => 'Autre',
    ];
}
