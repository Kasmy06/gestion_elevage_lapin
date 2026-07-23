<?php

namespace App\Models;

use App\Models\Concerns\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pesee extends Model
{
    /** @use HasFactory<\Database\Factories\PeseeFactory> */
    use HasFactory, LogsActivity;

    protected $fillable = [
        'lapin_id',
        'date_pesee',
        'poids_g',
    ];

    protected function casts(): array
    {
        return [
            'date_pesee' => 'date',
        ];
    }

    public function lapin(): BelongsTo
    {
        return $this->belongsTo(Lapin::class);
    }

    public function activityLogLabel(): string
    {
        return 'Pesée de '.($this->lapin?->identifiant ?? '#'.$this->lapin_id).' : '.$this->poids_g.' g';
    }
}
