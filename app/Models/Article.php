<?php

namespace App\Models;

use App\Enums\ArticleStatus;
use App\Enums\UnitType;
use App\Traits\HasUnit;
use App\Traits\Sluggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Article extends Model
{
    use HasFactory, HasUnit, Sluggable, SoftDeletes;

    protected $fillable = [
        'unit_type',
        'unit_id',
        'category_id',
        'author_id',
        'title',
        'slug',
        'excerpt',
        'content',
        'featured_image',
        'gallery',
        'status',
        'is_featured',
        'is_pinned',
        'published_at',
        'view_count',
        'meta_title',
        'meta_description',
    ];

    protected function casts(): array
    {
        return [
            'unit_type' => UnitType::class,
            'status' => ArticleStatus::class,
            'gallery' => 'array',
            'is_featured' => 'boolean',
            'is_pinned' => 'boolean',
            'published_at' => 'datetime',
        ];
    }

    /**
     * Field used for slug generation
     */
    protected string $slugSource = 'title';

    /**
     * Get the category
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(ArticleCategory::class, 'category_id');
    }

    /**
     * Get the author
     */
    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    /**
     * Increment view count
     */
    public function incrementViewCount(): void
    {
        $this->increment('view_count');
    }

    /**
     * Check if article is published
     */
    public function isPublished(): bool
    {
        return $this->status === ArticleStatus::PUBLISHED
            && $this->published_at
            && $this->published_at->isPast();
    }

    /**
     * Get reading time in minutes
     */
    public function getReadingTimeAttribute(): int
    {
        $wordCount = str_word_count(strip_tags($this->content));
        return max(1, ceil($wordCount / 200));
    }

    /**
     * Scope for published articles
     */
    public function scopePublished($query)
    {
        return $query->where('status', ArticleStatus::PUBLISHED)
            ->where('published_at', '<=', now());
    }

    /**
     * Scope for featured articles
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope for pinned articles
     */
    public function scopePinned($query)
    {
        return $query->where('is_pinned', true);
    }

    /**
     * Scope ordered by published date
     */
    public function scopeLatest($query)
    {
        return $query->orderByDesc('is_pinned')
            ->orderByDesc('published_at');
    }

    /**
     * Scope by category
     */
    public function scopeByCategory($query, int $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }
}
