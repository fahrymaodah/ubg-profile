<?php

namespace App\Models;

use App\Enums\UnitType;
use App\Traits\HasUnit;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContactMessage extends Model
{
    use HasFactory, HasUnit;

    protected $fillable = [
        'unit_type',
        'unit_id',
        'name',
        'email',
        'phone',
        'subject',
        'message',
        'status',
        'replied_at',
        'replied_by',
        'ip_address',
        'user_agent',
    ];

    protected function casts(): array
    {
        return [
            'unit_type' => UnitType::class,
            'replied_at' => 'datetime',
        ];
    }

    /**
     * Get the user who replied
     */
    public function repliedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'replied_by');
    }

    /**
     * Mark as read
     */
    public function markAsRead(): void
    {
        if ($this->status === 'unread') {
            $this->update(['status' => 'read']);
        }
    }

    /**
     * Mark as replied
     */
    public function markAsReplied(?int $userId = null): void
    {
        $this->update([
            'status' => 'replied',
            'replied_at' => now(),
            'replied_by' => $userId ?? auth()->id(),
        ]);
    }

    /**
     * Check if unread
     */
    public function isUnread(): bool
    {
        return $this->status === 'unread';
    }

    /**
     * Get status color
     */
    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'unread' => 'danger',
            'read' => 'warning',
            'replied' => 'success',
            default => 'gray',
        };
    }

    /**
     * Scope for unread messages
     */
    public function scopeUnread($query)
    {
        return $query->where('status', 'unread');
    }

    /**
     * Scope for read messages
     */
    public function scopeRead($query)
    {
        return $query->where('status', 'read');
    }

    /**
     * Scope for replied messages
     */
    public function scopeReplied($query)
    {
        return $query->where('status', 'replied');
    }

    /**
     * Scope ordered by date
     */
    public function scopeLatest($query)
    {
        return $query->orderByDesc('created_at');
    }
}
