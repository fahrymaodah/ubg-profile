<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivityLog extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'unit_type',
        'unit_id',
        'action',
        'model_type',
        'model_id',
        'old_values',
        'new_values',
        'ip_address',
        'user_agent',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'old_values' => 'array',
            'new_values' => 'array',
            'created_at' => 'datetime',
        ];
    }

    /**
     * Get the user
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the subject model
     */
    public function subject()
    {
        if (!$this->model_type || !$this->model_id) {
            return null;
        }

        return $this->model_type::find($this->model_id);
    }

    /**
     * Get action color
     */
    public function getActionColorAttribute(): string
    {
        return match ($this->action) {
            'created' => 'success',
            'updated' => 'warning',
            'deleted' => 'danger',
            'restored' => 'info',
            'login' => 'primary',
            'logout' => 'gray',
            default => 'gray',
        };
    }

    /**
     * Log an activity
     */
    public static function log(
        string $action,
        ?Model $model = null,
        ?array $oldValues = null,
        ?array $newValues = null
    ): self {
        $user = auth()->user();

        return static::create([
            'user_id' => $user?->id,
            'unit_type' => $user?->unit_type?->value,
            'unit_id' => $user?->unit_id,
            'action' => $action,
            'model_type' => $model ? get_class($model) : null,
            'model_id' => $model?->id,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'created_at' => now(),
        ]);
    }

    /**
     * Scope by user
     */
    public function scopeByUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope by action
     */
    public function scopeByAction($query, string $action)
    {
        return $query->where('action', $action);
    }

    /**
     * Scope by model
     */
    public function scopeByModel($query, string $modelType, ?int $modelId = null)
    {
        $query->where('model_type', $modelType);
        
        if ($modelId) {
            $query->where('model_id', $modelId);
        }
        
        return $query;
    }

    /**
     * Scope ordered
     */
    public function scopeLatest($query)
    {
        return $query->orderByDesc('created_at');
    }
}
