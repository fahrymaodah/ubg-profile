<?php

namespace App\Models;

use App\Enums\UnitType;
use App\Traits\HasUnit;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gallery extends Model
{
    use HasFactory, HasUnit;

    protected $fillable = [
        'unit_type',
        'unit_id',
        'title',
        'description',
        'type',
        'file',
        'youtube_url',
        'is_featured',
        'is_active',
        'order',
    ];

    protected function casts(): array
    {
        return [
            'unit_type' => UnitType::class,
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Check if this is an image
     */
    public function isImage(): bool
    {
        return $this->type === 'image';
    }

    /**
     * Check if this is a video
     */
    public function isVideo(): bool
    {
        return $this->type === 'video';
    }

    /**
     * Get YouTube video ID from URL
     */
    public function getYoutubeIdAttribute(): ?string
    {
        if (!$this->youtube_url) {
            return null;
        }

        preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/', $this->youtube_url, $matches);
        
        return $matches[1] ?? null;
    }

    /**
     * Get YouTube thumbnail URL
     */
    public function getYoutubeThumbnailAttribute(): ?string
    {
        $id = $this->youtube_id;
        
        return $id ? "https://img.youtube.com/vi/{$id}/maxresdefault.jpg" : null;
    }

    /**
     * Get YouTube embed URL
     */
    public function getYoutubeEmbedUrlAttribute(): ?string
    {
        $id = $this->youtube_id;
        
        return $id ? "https://www.youtube.com/embed/{$id}" : null;
    }

    /**
     * Scope for active galleries
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for featured galleries
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope for images only
     */
    public function scopeImages($query)
    {
        return $query->where('type', 'image');
    }

    /**
     * Scope for videos only
     */
    public function scopeVideos($query)
    {
        return $query->where('type', 'video');
    }

    /**
     * Scope ordered
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('order');
    }
}
