<?php

namespace App\Models;

use App\Models\Concerns\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Employe extends Model
{
    /** @use HasFactory<\Database\Factories\EmployeFactory> */
    use HasFactory, SoftDeletes, LogsActivity;

    protected $fillable = [
        'nom',
        'poste',
        'telephone',
        'email',
        'date_embauche',
        'salaire',
        'statut',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'date_embauche' => 'date',
            'salaire' => 'decimal:2',
        ];
    }

    public function activityLogLabel(): string
    {
        return $this->nom;
    }

    public const STATUTS = [
        'actif' => 'Actif',
        'inactif' => 'Inactif',
    ];
}
