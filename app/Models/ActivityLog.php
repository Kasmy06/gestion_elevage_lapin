<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivityLog extends Model
{
    const UPDATED_AT = null;

    protected $fillable = [
        'user_id',
        'user_name',
        'user_email',
        'action',
        'subject_type',
        'subject_id',
        'subject_label',
        'changes',
    ];

    protected function casts(): array
    {
        return [
            'changes' => 'array',
            'created_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public const ACTIONS = [
        'created' => 'Création',
        'updated' => 'Modification',
        'deleted' => 'Suppression',
        'restored' => 'Restauration',
        'force_deleted' => 'Suppression définitive',
    ];

    public const MODULES = [
        Lapin::class => 'Lapin',
        Race::class => 'Race',
        Cage::class => 'Clapier',
        Aliment::class => 'Aliment',
        MouvementAliment::class => 'Mouvement de stock',
        Saillie::class => 'Saillie',
        MiseBas::class => 'Mise bas',
        Sevrage::class => 'Sevrage',
        Pesee::class => 'Pesée',
        SanteIntervention::class => 'Suivi santé',
        Sortie::class => 'Sortie',
        Client::class => 'Client',
        Vente::class => 'Vente',
        Depense::class => 'Dépense',
        Employe::class => 'Employé',
        Parametre::class => 'Paramètres',
        User::class => 'Utilisateur',
    ];

    public function moduleLabel(): string
    {
        return self::MODULES[$this->subject_type] ?? class_basename($this->subject_type);
    }
}
