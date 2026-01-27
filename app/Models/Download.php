<?php

namespace App\Models;

use App\Enums\UnitType;
use App\Traits\HasUnit;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Download extends Model
{
    use HasFactory, HasUnit;

    protected $fillable = [
        'unit_type',
        'unit_id',
        'category',
        'title',
        'description',
        'file',
        'file_size',
        'download_count',
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
     * Get file URL
     */
    public function getFileUrlAttribute(): string
    {
        return Storage::url($this->file);
    }

    /**
     * Get formatted file size
     */
    public function getFormattedSizeAttribute(): string
    {
        $bytes = $this->file_size;
        
        if ($bytes === null || $bytes === 0) {
            return '0 B';
        }

        $units = ['B', 'KB', 'MB', 'GB'];
        $factor = floor((strlen($bytes) - 1) / 3);
        
        return sprintf("%.2f %s", $bytes / pow(1024, $factor), $units[$factor]);
    }

    /**
     * Get file extension
     */
    public function getExtensionAttribute(): string
    {
        return pathinfo($this->file, PATHINFO_EXTENSION);
    }

    /**
     * Increment download count
     */
    public function incrementDownloadCount(): void
    {
        $this->increment('download_count');
    }

    /**
     * Get available categories for a unit
     */
    public static function categoriesForUnit(UnitType $unitType, ?int $unitId = null): array
    {
        return static::forUnit($unitType, $unitId)
            ->whereNotNull('category')
            ->distinct()
            ->pluck('category')
            ->toArray();
    }

    /**
     * Scope for active downloads
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope by category
     */
    public function scopeByCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Scope ordered
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('order')->orderBy('title');
    }
}
