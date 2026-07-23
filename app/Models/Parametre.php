<?php

namespace App\Models;

use App\Models\Concerns\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Parametre extends Model
{
    /** @use HasFactory<\Database\Factories\ParametreFactory> */
    use HasFactory, LogsActivity;

    protected $fillable = [
        'nom_ferme',
        'devise',
    ];

    /**
     * Les paramètres de l'application tiennent en une seule ligne (id=1),
     * créée avec ses valeurs par défaut si elle n'existe pas encore.
     */
    public static function current(): self
    {
        return static::query()->firstOrCreate(
            ['id' => 1],
            ['nom_ferme' => "Élevage de Lapins", 'devise' => 'FCFA']
        );
    }

    public function activityLogLabel(): string
    {
        return $this->nom_ferme;
    }
}
