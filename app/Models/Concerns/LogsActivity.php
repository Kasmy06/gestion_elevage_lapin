<?php

namespace App\Models\Concerns;

use App\Models\ActivityLog;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Journalise les créations, modifications et suppressions du modèle dans
 * activity_logs, pour la traçabilité des actions par utilisateur.
 */
trait LogsActivity
{
    protected static function bootLogsActivity(): void
    {
        static::created(function ($model) {
            $model->recordActivity('created');
        });

        static::updated(function ($model) {
            $changes = $model->activityLogChanges();

            if (empty($changes)) {
                return;
            }

            $model->recordActivity('updated', $changes);
        });

        static::deleted(function ($model) {
            if (property_exists($model, 'forceDeleting') && $model->forceDeleting) {
                return;
            }

            $model->recordActivity('deleted');
        });

        if (in_array(SoftDeletes::class, class_uses_recursive(static::class), true)) {
            static::restored(function ($model) {
                $model->recordActivity('restored');
            });

            static::forceDeleted(function ($model) {
                $model->recordActivity('force_deleted');
            });
        }
    }

    /**
     * Champs totalement ignorés du journal (bruit technique sans intérêt pour l'audit).
     * Surchargeable par modèle via la propriété $logExcept.
     */
    protected function activityLogExcept(): array
    {
        return array_merge(
            ['created_at', 'updated_at', 'deleted_at', 'remember_token'],
            property_exists($this, 'logExcept') ? $this->logExcept : []
        );
    }

    /**
     * Champs dont le changement doit apparaître dans le journal sans jamais
     * exposer la valeur (ex : mot de passe). Surchargeable via $logRedacted.
     */
    protected function activityLogRedacted(): array
    {
        return array_merge(
            ['password'],
            property_exists($this, 'logRedacted') ? $this->logRedacted : []
        );
    }

    protected function activityLogChanges(): array
    {
        $except = $this->activityLogExcept();
        $redacted = $this->activityLogRedacted();
        $diff = [];

        foreach ($this->getChanges() as $key => $newValue) {
            if (in_array($key, $except, true)) {
                continue;
            }

            if (in_array($key, $redacted, true)) {
                $diff[$key] = ['avant' => '••••••', 'apres' => '••••••'];

                continue;
            }

            $diff[$key] = [
                'avant' => $this->getOriginal($key),
                'apres' => $newValue,
            ];
        }

        return $diff;
    }

    /**
     * Libellé lisible identifiant l'enregistrement dans le journal.
     * À surcharger par modèle pour un rendu plus parlant qu'un simple id.
     */
    public function activityLogLabel(): string
    {
        return '#'.$this->getKey();
    }

    protected function recordActivity(string $action, array $changes = []): void
    {
        $user = auth()->user();

        ActivityLog::create([
            'user_id' => $user?->id,
            'user_name' => $user?->name,
            'user_email' => $user?->email,
            'action' => $action,
            'subject_type' => static::class,
            'subject_id' => $this->getKey(),
            'subject_label' => $this->activityLogLabel(),
            'changes' => $changes ?: null,
        ]);
    }
}
