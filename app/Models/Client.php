<?php

namespace App\Models;

use App\Models\Concerns\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Client extends Model
{
    /** @use HasFactory<\Database\Factories\ClientFactory> */
    use HasFactory, SoftDeletes, LogsActivity;

    protected $fillable = [
        'nom',
        'telephone',
        'email',
        'adresse',
        'notes',
    ];

    public function ventes(): HasMany
    {
        return $this->hasMany(Vente::class);
    }

    public function activityLogLabel(): string
    {
        return $this->nom;
    }
}
