<?php

namespace App\Traits;

use App\Models\ActivityLog;

trait HasActivityLog
{
    /**
     * Boot the trait
     */
    protected static function bootHasActivityLog(): void
    {
        static::created(function ($model) {
            if (static::shouldLogActivity()) {
                ActivityLog::log(
                    action: 'created',
                    model: $model,
                    newValues: $model->getActivityLogAttributes()
                );
            }
        });

        static::updated(function ($model) {
            if (static::shouldLogActivity()) {
                $changes = $model->getChanges();
                $original = collect($model->getOriginal())->only(array_keys($changes))->toArray();
                
                ActivityLog::log(
                    action: 'updated',
                    model: $model,
                    oldValues: $original,
                    newValues: $changes
                );
            }
        });

        static::deleted(function ($model) {
            if (static::shouldLogActivity()) {
                ActivityLog::log(
                    action: 'deleted',
                    model: $model,
                    oldValues: $model->getActivityLogAttributes()
                );
            }
        });

        // If model uses SoftDeletes
        if (method_exists(static::class, 'restored')) {
            static::restored(function ($model) {
                if (static::shouldLogActivity()) {
                    ActivityLog::log(
                        action: 'restored',
                        model: $model
                    );
                }
            });
        }
    }

    /**
     * Check if activity should be logged
     */
    protected static function shouldLogActivity(): bool
    {
        // Can be overridden in models to conditionally disable logging
        return config('app.activity_log_enabled', true);
    }

    /**
     * Get attributes to log
     */
    protected function getActivityLogAttributes(): array
    {
        // Exclude sensitive fields
        $exclude = $this->activityLogExclude ?? ['password', 'remember_token'];
        
        return collect($this->getAttributes())
            ->except($exclude)
            ->toArray();
    }

    /**
     * Get activity logs for this model
     */
    public function activityLogs()
    {
        return ActivityLog::where('model_type', get_class($this))
            ->where('model_id', $this->id)
            ->latest()
            ->get();
    }

    /**
     * Get latest activity for this model
     */
    public function latestActivity(): ?ActivityLog
    {
        return ActivityLog::where('model_type', get_class($this))
            ->where('model_id', $this->id)
            ->latest()
            ->first();
    }
}
