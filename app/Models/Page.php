<?php

namespace App\Models;

use App\Enums\UnitType;
use App\Traits\HasUnit;
use App\Traits\Sluggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    use HasFactory, HasUnit, Sluggable;

    protected $fillable = [
        'unit_type',
        'unit_id',
        'title',
        'slug',
        'content',
        'template',
        'is_active',
        'meta_title',
        'meta_description',
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
    protected string $slugSource = 'title';

    /**
     * Available templates
     */
    public static function templates(): array
    {
        return [
            'default' => 'Default',
            'full-width' => 'Full Width',
            'sidebar-left' => 'Sidebar Left',
            'sidebar-right' => 'Sidebar Right',
            'landing' => 'Landing Page',
        ];
    }

    /**
     * Scope for active pages
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Find by slug for unit
     */
    public static function findBySlug(string $slug, UnitType $unitType, ?int $unitId = null): ?self
    {
        return static::forUnit($unitType, $unitId)
            ->where('slug', $slug)
            ->active()
            ->first();
    }
}
