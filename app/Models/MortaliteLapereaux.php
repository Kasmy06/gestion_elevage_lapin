<?php

namespace App\Models;

use App\Models\Concerns\LogsActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MortaliteLapereaux extends Model
{
    use LogsActivity;

    protected $table = 'mortalites_lapereaux';

    public const STADE_NAISSANCE = 'naissance';

    public const STADE_SEVRAGE = 'sevrage';

    public const STADES = [
        self::STADE_NAISSANCE => 'Après naissance',
        self::STADE_SEVRAGE => 'Après sevrage',
    ];

    protected $fillable = [
        'mise_bas_id',
        'date',
        'stade',
        'nombre',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
        ];
    }

    public function miseBas(): BelongsTo
    {
        return $this->belongsTo(MiseBas::class);
    }

    public function activityLogLabel(): string
    {
        return 'Mortalité de '.$this->nombre.' lapereau(x) ('.self::STADES[$this->stade].')';
    }
}
