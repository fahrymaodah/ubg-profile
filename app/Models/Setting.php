<?php

namespace App\Models;

use App\Enums\UnitType;
use App\Traits\HasUnit;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    use HasFactory, HasUnit;

    protected $fillable = [
        'unit_type',
        'unit_id',
        'key',
        'value',
        'type',
    ];

    protected function casts(): array
    {
        return [
            'unit_type' => UnitType::class,
        ];
    }

    /**
     * Get setting value
     */
    public static function getValue(string $key, UnitType $unitType = UnitType::UNIVERSITAS, ?int $unitId = null, $default = null)
    {
        $cacheKey = "setting.{$unitType->value}.{$unitId}.{$key}";
        
        return Cache::remember($cacheKey, 3600, function () use ($key, $unitType, $unitId, $default) {
            $setting = static::forUnit($unitType, $unitId)
                ->where('key', $key)
                ->first();
            
            if (!$setting) {
                return $default;
            }
            
            return static::castValue($setting->value, $setting->type);
        });
    }

    /**
     * Set setting value
     */
    public static function setValue(string $key, $value, UnitType $unitType = UnitType::UNIVERSITAS, ?int $unitId = null, string $type = 'text'): void
    {
        static::updateOrCreate(
            [
                'unit_type' => $unitType,
                'unit_id' => $unitId,
                'key' => $key,
            ],
            [
                'value' => is_array($value) ? json_encode($value) : $value,
                'type' => $type,
            ]
        );

        // Clear cache
        $cacheKey = "setting.{$unitType->value}.{$unitId}.{$key}";
        Cache::forget($cacheKey);
    }

    /**
     * Get multiple settings
     */
    public static function getMany(array $keys, UnitType $unitType = UnitType::UNIVERSITAS, ?int $unitId = null): array
    {
        $settings = static::forUnit($unitType, $unitId)
            ->whereIn('key', $keys)
            ->pluck('value', 'key')
            ->toArray();
        
        return array_merge(array_fill_keys($keys, null), $settings);
    }

    /**
     * Get all settings for a unit
     */
    public static function getAllForUnit(UnitType $unitType = UnitType::UNIVERSITAS, ?int $unitId = null): array
    {
        return static::forUnit($unitType, $unitId)
            ->pluck('value', 'key')
            ->toArray();
    }

    /**
     * Cast value based on type
     */
    protected static function castValue($value, string $type)
    {
        return match ($type) {
            'boolean' => filter_var($value, FILTER_VALIDATE_BOOLEAN),
            'integer' => (int) $value,
            'float' => (float) $value,
            'json', 'array' => json_decode($value, true),
            default => $value,
        };
    }

    /**
     * Clear all settings cache for a unit
     */
    public static function clearCache(UnitType $unitType = UnitType::UNIVERSITAS, ?int $unitId = null): void
    {
        // This is a simplified approach - in production you might want to use cache tags
        Cache::flush();
    }
}
