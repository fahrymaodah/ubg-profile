<?php

namespace App\Traits;

use Illuminate\Support\Str;

trait Sluggable
{
    /**
     * Boot the trait
     */
    protected static function bootSluggable(): void
    {
        static::creating(function ($model) {
            if (empty($model->slug)) {
                $model->slug = $model->generateUniqueSlug();
            }
        });

        static::updating(function ($model) {
            $sourceField = $model->slugSource ?? 'name';
            
            // Regenerate slug if source field changed and slug wasn't manually set
            if ($model->isDirty($sourceField) && !$model->isDirty('slug')) {
                $model->slug = $model->generateUniqueSlug();
            }
        });
    }

    /**
     * Generate a unique slug
     */
    public function generateUniqueSlug(): string
    {
        $sourceField = $this->slugSource ?? 'name';
        $baseSlug = Str::slug($this->{$sourceField});
        
        $slug = $baseSlug;
        $counter = 1;

        // Build query based on model
        $query = static::where('slug', $slug);
        
        // If updating, exclude current model
        if ($this->exists) {
            $query->where($this->getKeyName(), '!=', $this->getKey());
        }

        // If model has unit, ensure slug is unique within unit
        if (in_array(HasUnit::class, class_uses_recursive($this))) {
            if ($this->unit_type) {
                $query->where('unit_type', $this->unit_type)
                      ->where('unit_id', $this->unit_id);
            }
        }

        while ($query->exists()) {
            $slug = $baseSlug . '-' . $counter++;
            
            $query = static::where('slug', $slug);
            
            if ($this->exists) {
                $query->where($this->getKeyName(), '!=', $this->getKey());
            }
            
            if (in_array(HasUnit::class, class_uses_recursive($this))) {
                if ($this->unit_type) {
                    $query->where('unit_type', $this->unit_type)
                          ->where('unit_id', $this->unit_id);
                }
            }
        }

        return $slug;
    }

    /**
     * Get the route key name
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /**
     * Find by slug
     */
    public static function findBySlug(string $slug): ?self
    {
        return static::where('slug', $slug)->first();
    }

    /**
     * Find by slug or fail
     */
    public static function findBySlugOrFail(string $slug): self
    {
        return static::where('slug', $slug)->firstOrFail();
    }
}
