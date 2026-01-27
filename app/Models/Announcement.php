<?php

namespace App\Models;

use App\Enums\UnitType;
use App\Traits\HasUnit;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    use HasFactory, HasUnit;

    protected $fillable = [
        'unit_type',
        'unit_id',
        'title',
        'content',
        'priority',
        'start_date',
        'end_date',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'unit_type' => UnitType::class,
            'start_date' => 'datetime',
            'end_date' => 'datetime',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Priority levels
     */
    public static function priorities(): array
    {
        return [
            'low' => 'Low',
            'normal' => 'Normal',
            'high' => 'High',
            'urgent' => 'Urgent',
        ];
    }

    /**
     * Get priority color
     */
    public function getPriorityColorAttribute(): string
    {
        return match ($this->priority) {
            'low' => 'gray',
            'normal' => 'info',
            'high' => 'warning',
            'urgent' => 'danger',
            default => 'gray',
        };
    }

    /**
     * Check if announcement is currently visible
     */
    public function isCurrentlyVisible(): bool
    {
        if (!$this->is_active) {
            return false;
        }

        $now = now();

        if ($this->start_date && $now->lt($this->start_date)) {
            return false;
        }

        if ($this->end_date && $now->gt($this->end_date)) {
            return false;
        }

        return true;
    }

    /**
     * Scope for active announcements
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for currently visible announcements
     */
    public function scopeVisible($query)
    {
        return $query->active()
            ->where(function ($q) {
                $q->whereNull('start_date')
                  ->orWhere('start_date', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('end_date')
                  ->orWhere('end_date', '>=', now());
            });
    }

    /**
     * Scope by priority
     */
    public function scopeByPriority($query, string $priority)
    {
        return $query->where('priority', $priority);
    }

    /**
     * Scope ordered by priority and date
     */
    public function scopeOrdered($query)
    {
        // FIELD() is MySQL-specific, use CASE for cross-database compatibility
        if (config('database.default') === 'sqlite') {
            return $query->orderByRaw("
                CASE priority 
                    WHEN 'urgent' THEN 1 
                    WHEN 'high' THEN 2 
                    WHEN 'normal' THEN 3 
                    WHEN 'low' THEN 4 
                    ELSE 5 
                END
            ")->orderByDesc('created_at');
        }
        
        return $query->orderByRaw("FIELD(priority, 'urgent', 'high', 'normal', 'low')")
            ->orderByDesc('created_at');
    }
}
