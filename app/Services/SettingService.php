<?php

namespace App\Services;

use App\Enums\UnitType;
use App\Models\Setting;
use Illuminate\Support\Facades\Cache;

class SettingService
{
    /**
     * Cache TTL in seconds
     */
    protected int $cacheTtl = 3600;

    /**
     * Default settings schema
     */
    protected array $settingsSchema = [
        // General
        'site_name' => ['type' => 'text', 'default' => 'Universitas Bumigora'],
        'site_description' => ['type' => 'textarea', 'default' => ''],
        'site_keywords' => ['type' => 'text', 'default' => ''],
        'logo' => ['type' => 'image', 'default' => null],
        'logo_dark' => ['type' => 'image', 'default' => null],
        'favicon' => ['type' => 'image', 'default' => null],
        
        // Contact
        'address' => ['type' => 'textarea', 'default' => ''],
        'phone' => ['type' => 'text', 'default' => ''],
        'email' => ['type' => 'email', 'default' => ''],
        'whatsapp' => ['type' => 'text', 'default' => ''],
        'maps_embed' => ['type' => 'textarea', 'default' => ''],
        'maps_link' => ['type' => 'url', 'default' => ''],
        
        // Social Media
        'facebook' => ['type' => 'url', 'default' => ''],
        'twitter' => ['type' => 'url', 'default' => ''],
        'instagram' => ['type' => 'url', 'default' => ''],
        'youtube' => ['type' => 'url', 'default' => ''],
        'linkedin' => ['type' => 'url', 'default' => ''],
        'tiktok' => ['type' => 'url', 'default' => ''],
        
        // Theme
        'theme_color_primary' => ['type' => 'color', 'default' => '#1e40af'],
        'theme_color_secondary' => ['type' => 'color', 'default' => '#64748b'],
        'theme_color_accent' => ['type' => 'color', 'default' => '#f59e0b'],
        'theme_font_heading' => ['type' => 'text', 'default' => 'Inter'],
        'theme_font_body' => ['type' => 'text', 'default' => 'Inter'],
        'custom_css' => ['type' => 'code', 'default' => ''],
        'custom_js' => ['type' => 'code', 'default' => ''],
        
        // Features
        'show_announcement_bar' => ['type' => 'boolean', 'default' => true],
        'show_floating_whatsapp' => ['type' => 'boolean', 'default' => true],
        'show_back_to_top' => ['type' => 'boolean', 'default' => true],
        'enable_dark_mode' => ['type' => 'boolean', 'default' => false],
        
        // Content limits
        'articles_per_page' => ['type' => 'integer', 'default' => 12],
        'featured_articles_count' => ['type' => 'integer', 'default' => 4],
        'sidebar_articles_count' => ['type' => 'integer', 'default' => 5],
        
        // Footer
        'footer_text' => ['type' => 'text', 'default' => ''],
        'footer_links' => ['type' => 'json', 'default' => []],
    ];

    /**
     * Get a setting value
     */
    public function get(string $key, UnitType $unitType = UnitType::UNIVERSITAS, ?int $unitId = null, $default = null)
    {
        $cacheKey = $this->getCacheKey($key, $unitType, $unitId);

        return Cache::remember($cacheKey, $this->cacheTtl, function () use ($key, $unitType, $unitId, $default) {
            // Try to get setting for specific unit
            $value = Setting::getValue($key, $unitType, $unitId);
            
            if ($value !== null) {
                return $value;
            }

            // Fall back to parent unit (inheritance)
            $inheritedValue = $this->getInheritedValue($key, $unitType, $unitId);
            
            if ($inheritedValue !== null) {
                return $inheritedValue;
            }

            // Fall back to default from schema
            return $default ?? ($this->settingsSchema[$key]['default'] ?? null);
        });
    }

    /**
     * Get inherited value from parent units
     */
    protected function getInheritedValue(string $key, UnitType $unitType, ?int $unitId)
    {
        if ($unitType === UnitType::PRODI && $unitId) {
            // Try fakultas level
            $prodi = \App\Models\Prodi::find($unitId);
            if ($prodi && $prodi->fakultas_id) {
                $value = Setting::getValue($key, UnitType::FAKULTAS, $prodi->fakultas_id);
                if ($value !== null) {
                    return $value;
                }
            }
        }

        if ($unitType !== UnitType::UNIVERSITAS) {
            // Try universitas level
            $value = Setting::getValue($key, UnitType::UNIVERSITAS, null);
            if ($value !== null) {
                return $value;
            }
        }

        return null;
    }

    /**
     * Set a setting value
     */
    public function set(string $key, $value, UnitType $unitType = UnitType::UNIVERSITAS, ?int $unitId = null): void
    {
        $type = $this->settingsSchema[$key]['type'] ?? 'text';
        
        Setting::setValue($key, $value, $unitType, $unitId, $type);
        
        $this->clearCache($key, $unitType, $unitId);
    }

    /**
     * Get multiple settings
     */
    public function getMany(array $keys, UnitType $unitType = UnitType::UNIVERSITAS, ?int $unitId = null): array
    {
        $result = [];
        
        foreach ($keys as $key) {
            $result[$key] = $this->get($key, $unitType, $unitId);
        }
        
        return $result;
    }

    /**
     * Get all settings for a unit
     */
    public function getAllForUnit(UnitType $unitType = UnitType::UNIVERSITAS, ?int $unitId = null): array
    {
        $cacheKey = "settings.all.{$unitType->value}.{$unitId}";

        return Cache::remember($cacheKey, $this->cacheTtl, function () use ($unitType, $unitId) {
            $result = [];
            
            foreach (array_keys($this->settingsSchema) as $key) {
                $result[$key] = $this->get($key, $unitType, $unitId);
            }
            
            return $result;
        });
    }

    /**
     * Get settings schema
     */
    public function getSchema(): array
    {
        return $this->settingsSchema;
    }

    /**
     * Get settings grouped by category (for admin forms)
     */
    public function getSchemaGrouped(): array
    {
        return [
            'General' => [
                'site_name', 'site_description', 'site_keywords',
                'logo', 'logo_dark', 'favicon',
            ],
            'Contact' => [
                'address', 'phone', 'email', 'whatsapp',
                'maps_embed', 'maps_link',
            ],
            'Social Media' => [
                'facebook', 'twitter', 'instagram',
                'youtube', 'linkedin', 'tiktok',
            ],
            'Theme' => [
                'theme_color_primary', 'theme_color_secondary', 'theme_color_accent',
                'theme_font_heading', 'theme_font_body',
                'custom_css', 'custom_js',
            ],
            'Features' => [
                'show_announcement_bar', 'show_floating_whatsapp',
                'show_back_to_top', 'enable_dark_mode',
            ],
            'Content' => [
                'articles_per_page', 'featured_articles_count', 'sidebar_articles_count',
            ],
            'Footer' => [
                'footer_text', 'footer_links',
            ],
        ];
    }

    /**
     * Check if setting exists in schema
     */
    public function exists(string $key): bool
    {
        return isset($this->settingsSchema[$key]);
    }

    /**
     * Get cache key
     */
    protected function getCacheKey(string $key, UnitType $unitType, ?int $unitId): string
    {
        return "setting.{$key}.{$unitType->value}.{$unitId}";
    }

    /**
     * Clear cache for a specific setting
     */
    public function clearCache(string $key, UnitType $unitType, ?int $unitId = null): void
    {
        Cache::forget($this->getCacheKey($key, $unitType, $unitId));
        Cache::forget("settings.all.{$unitType->value}.{$unitId}");
    }

    /**
     * Clear all settings cache
     */
    public function clearAllCache(): void
    {
        Cache::flush();
    }

    /**
     * Get logo with cascading logic
     * 
     * Priority for Prodi: prodi.logo → fakultas.logo → universitas setting → default
     * Priority for Fakultas: fakultas.logo → universitas setting → default
     * Priority for Universitas: universitas setting → default
     *
     * @param string $type 'logo' | 'logo_dark' | 'favicon'
     * @param UnitType $unitType Current unit type
     * @param int|null $unitId Unit ID
     * @param mixed $unit The unit model (Fakultas or Prodi)
     * @return string URL to the logo
     */
    public function getCascadingLogo(string $type, UnitType $unitType, ?int $unitId, $unit = null): string
    {
        // Default logos
        $defaults = [
            'logo' => asset('images/logo-ubg-label.png'),
            'logo_dark' => asset('images/logo-ubg-label-white.png'),
            'favicon' => asset('images/logo-ubg.png'),
        ];

        $default = $defaults[$type] ?? $defaults['logo'];

        // For Prodi level
        if ($unitType === UnitType::PRODI && $unitId) {
            $prodi = $unit ?? \App\Models\Prodi::find($unitId);
            
            if ($prodi) {
                // Check prodi's own logo
                if ($prodi->logo) {
                    return \Illuminate\Support\Facades\Storage::url($prodi->logo);
                }

                // Check fakultas's logo
                if ($prodi->fakultas && $prodi->fakultas->logo) {
                    return \Illuminate\Support\Facades\Storage::url($prodi->fakultas->logo);
                }
            }

            // Check universitas setting
            $universitasSetting = Setting::getValue($type, UnitType::UNIVERSITAS, null);
            if ($universitasSetting) {
                return \Illuminate\Support\Facades\Storage::url($universitasSetting);
            }

            return $default;
        }

        // For Fakultas level
        if ($unitType === UnitType::FAKULTAS && $unitId) {
            $fakultas = $unit ?? \App\Models\Fakultas::find($unitId);
            
            if ($fakultas && $fakultas->logo) {
                return \Illuminate\Support\Facades\Storage::url($fakultas->logo);
            }

            // Check universitas setting
            $universitasSetting = Setting::getValue($type, UnitType::UNIVERSITAS, null);
            if ($universitasSetting) {
                return \Illuminate\Support\Facades\Storage::url($universitasSetting);
            }

            return $default;
        }

        // For Universitas level
        $universitasSetting = $this->get($type, UnitType::UNIVERSITAS, null);
        if ($universitasSetting) {
            return \Illuminate\Support\Facades\Storage::url($universitasSetting);
        }

        return $default;
    }

    /**
     * Get profil data (visi, misi, sejarah, struktur) for a unit
     */
    public function getProfilData(UnitType $unitType, ?int $unitId, $unit = null): array
    {
        if ($unitType === UnitType::UNIVERSITAS) {
            // Load from settings
            $settings = Setting::query()
                ->where('unit_type', UnitType::UNIVERSITAS)
                ->whereNull('unit_id')
                ->whereIn('key', [
                    'profil_visi',
                    'profil_misi',
                    'profil_tujuan',
                    'profil_sejarah',
                    'profil_struktur_organisasi',
                    'profil_struktur_image',
                ])
                ->pluck('value', 'key')
                ->toArray();

            // Parse JSON fields
            $misi = json_decode($settings['profil_misi'] ?? '[]', true) ?: [];
            $tujuan = json_decode($settings['profil_tujuan'] ?? '[]', true) ?: [];
            $sejarah = json_decode($settings['profil_sejarah'] ?? '{}', true) ?: [];
            $struktur = json_decode($settings['profil_struktur_organisasi'] ?? '{}', true) ?: [];

            return [
                'visi' => $settings['profil_visi'] ?? '',
                'misi' => $misi,
                'tujuan' => $tujuan,
                'sejarah' => $sejarah,
                'struktur' => $struktur,
                'struktur_image' => $settings['profil_struktur_image'] ?? null,
            ];
        }

        // For Fakultas/Prodi - load from unit model
        if (!$unit) {
            if ($unitType === UnitType::FAKULTAS) {
                $unit = \App\Models\Fakultas::find($unitId);
            } elseif ($unitType === UnitType::PRODI) {
                $unit = \App\Models\Prodi::find($unitId);
            }
        }

        if (!$unit) {
            return [
                'visi' => '',
                'misi' => [],
                'tujuan' => [],
                'sejarah' => [],
                'struktur' => [],
                'struktur_image' => null,
            ];
        }

        // Parse JSON fields from unit
        $misi = json_decode($unit->misi ?? '[]', true) ?: [];
        $tujuan = json_decode($unit->tujuan ?? '[]', true) ?: [];
        $sejarah = json_decode($unit->sejarah ?? '{}', true) ?: [];
        $struktur = json_decode($unit->struktur_organisasi ?? '{}', true) ?: [];

        return [
            'visi' => $unit->visi ?? '',
            'misi' => $misi,
            'tujuan' => $tujuan,
            'sejarah' => $sejarah,
            'struktur' => $struktur,
            'struktur_image' => $unit->struktur_image ?? null,
        ];
    }
}
