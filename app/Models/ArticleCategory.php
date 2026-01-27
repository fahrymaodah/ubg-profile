<?php

namespace App\Models;

use App\Enums\UnitType;
use App\Traits\HasUnit;
use App\Traits\Sluggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ArticleCategory extends Model
{
    use HasFactory, HasUnit, Sluggable;

    protected $fillable = [
        'unit_type',
        'unit_id',
        'name',
        'slug',
        'description',
        'is_active',
        'order',
    ];

    protected function casts(): array
    {
        return [
            'unit_type' => UnitType::class,
            'is_active' => 'boolean',
        ];
    }

    /**
     * Field used for slug generation
     */
    protected string $slugSource = 'name';

    /**
     * Get articles in this category
     */
    public function articles(): HasMany
    {
        return $this->hasMany(Article::class, 'category_id');
    }

    /**
     * Get published articles count
     */
    public function getPublishedArticlesCountAttribute(): int
    {
        return $this->articles()->published()->count();
    }

    /**
     * Scope for active categories
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope ordered
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('order')->orderBy('name');
    }
}
