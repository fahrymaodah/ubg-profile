<?php

namespace App\Models;

use App\Enums\UnitType;
use App\Traits\HasUnit;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory, HasUnit;

    protected $fillable = [
        'unit_type',
        'unit_id',
        'title',
        'description',
        'location',
        'start_date',
        'end_date',
        'image',
        'registration_link',
        'is_featured',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'unit_type' => UnitType::class,
            'start_date' => 'datetime',
            'end_date' => 'datetime',
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Check if event is upcoming
     */
    public function isUpcoming(): bool
    {
        return $this->start_date->isFuture();
    }

    /**
     * Check if event is ongoing
     */
    public function isOngoing(): bool
    {
        $now = now();
        return $this->start_date->lte($now) && 
               ($this->end_date ? $this->end_date->gte($now) : $this->start_date->isToday());
    }

    /**
     * Check if event is past
     */
    public function isPast(): bool
    {
        $endDate = $this->end_date ?? $this->start_date;
        return $endDate->isPast();
    }

    /**
     * Get event status
     */
    public function getStatusAttribute(): string
    {
        if ($this->isUpcoming()) {
            return 'upcoming';
        }
        if ($this->isOngoing()) {
            return 'ongoing';
        }
        return 'past';
    }

    /**
     * Scope for active events
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for featured events
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope for upcoming events
     */
    public function scopeUpcoming($query)
    {
        return $query->where('start_date', '>', now())
            ->orderBy('start_date');
    }

    /**
     * Scope for ongoing events
     */
    public function scopeOngoing($query)
    {
        return $query->where('start_date', '<=', now())
            ->where(function ($q) {
                $q->where('end_date', '>=', now())
                  ->orWhereNull('end_date');
            });
    }

    /**
     * Scope for past events
     */
    public function scopePast($query)
    {
        return $query->where(function ($q) {
            $q->where('end_date', '<', now())
              ->orWhere(function ($q2) {
                  $q2->whereNull('end_date')
                     ->where('start_date', '<', now()->startOfDay());
              });
        })->orderByDesc('start_date');
    }

    /**
     * Scope for events in date range
     */
    public function scopeInDateRange($query, $start, $end)
    {
        return $query->where('start_date', '>=', $start)
            ->where('start_date', '<=', $end);
    }
}
