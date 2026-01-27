<?php

namespace App\Services;

use App\Enums\UnitType;
use Illuminate\Support\Facades\Cache;

class ThemeService
{
    /**
     * Cache TTL in seconds
     */
    protected int $cacheTtl = 3600;

    /**
     * Settings service
     */
    protected SettingService $settingService;

    public function __construct(SettingService $settingService)
    {
        $this->settingService = $settingService;
    }

    /**
     * Get theme configuration for a unit
     */
    public function getThemeConfig(UnitType $unitType, ?int $unitId = null): array
    {
        $cacheKey = "theme.{$unitType->value}.{$unitId}";

        return Cache::remember($cacheKey, $this->cacheTtl, function () use ($unitType, $unitId) {
            return [
                'colors' => $this->getColors($unitType, $unitId),
                'fonts' => $this->getFonts($unitType, $unitId),
                'customCss' => $this->getCustomCss($unitType, $unitId),
                'customJs' => $this->getCustomJs($unitType, $unitId),
                'logo' => $this->getLogo($unitType, $unitId),
                'logoDark' => $this->getLogoDark($unitType, $unitId),
                'favicon' => $this->getFavicon($unitType, $unitId),
            ];
        });
    }

    /**
     * Get theme colors
     */
    public function getColors(UnitType $unitType, ?int $unitId = null): array
    {
        return [
            'primary' => $this->settingService->get('theme_color_primary', $unitType, $unitId),
            'secondary' => $this->settingService->get('theme_color_secondary', $unitType, $unitId),
            'accent' => $this->settingService->get('theme_color_accent', $unitType, $unitId),
        ];
    }

    /**
     * Get theme fonts
     */
    public function getFonts(UnitType $unitType, ?int $unitId = null): array
    {
        return [
            'heading' => $this->settingService->get('theme_font_heading', $unitType, $unitId),
            'body' => $this->settingService->get('theme_font_body', $unitType, $unitId),
        ];
    }

    /**
     * Get custom CSS (merged from parent units)
     */
    public function getCustomCss(UnitType $unitType, ?int $unitId = null): string
    {
        $css = '';

        // Always include universitas CSS
        $css .= $this->settingService->get('custom_css', UnitType::UNIVERSITAS, null) ?? '';

        // If fakultas, add fakultas CSS
        if ($unitType === UnitType::FAKULTAS && $unitId) {
            $css .= "\n" . ($this->settingService->get('custom_css', UnitType::FAKULTAS, $unitId) ?? '');
        }

        // If prodi, add fakultas and prodi CSS
        if ($unitType === UnitType::PRODI && $unitId) {
            $prodi = \App\Models\Prodi::find($unitId);
            if ($prodi && $prodi->fakultas_id) {
                $css .= "\n" . ($this->settingService->get('custom_css', UnitType::FAKULTAS, $prodi->fakultas_id) ?? '');
            }
            $css .= "\n" . ($this->settingService->get('custom_css', UnitType::PRODI, $unitId) ?? '');
        }

        return trim($css);
    }

    /**
     * Get custom JS (merged from parent units)
     */
    public function getCustomJs(UnitType $unitType, ?int $unitId = null): string
    {
        $js = '';

        // Always include universitas JS
        $js .= $this->settingService->get('custom_js', UnitType::UNIVERSITAS, null) ?? '';

        // If fakultas, add fakultas JS
        if ($unitType === UnitType::FAKULTAS && $unitId) {
            $js .= "\n" . ($this->settingService->get('custom_js', UnitType::FAKULTAS, $unitId) ?? '');
        }

        // If prodi, add fakultas and prodi JS
        if ($unitType === UnitType::PRODI && $unitId) {
            $prodi = \App\Models\Prodi::find($unitId);
            if ($prodi && $prodi->fakultas_id) {
                $js .= "\n" . ($this->settingService->get('custom_js', UnitType::FAKULTAS, $prodi->fakultas_id) ?? '');
            }
            $js .= "\n" . ($this->settingService->get('custom_js', UnitType::PRODI, $unitId) ?? '');
        }

        return trim($js);
    }

    /**
     * Get logo with inheritance
     */
    public function getLogo(UnitType $unitType, ?int $unitId = null): ?string
    {
        return $this->settingService->get('logo', $unitType, $unitId);
    }

    /**
     * Get dark logo with inheritance
     */
    public function getLogoDark(UnitType $unitType, ?int $unitId = null): ?string
    {
        return $this->settingService->get('logo_dark', $unitType, $unitId);
    }

    /**
     * Get favicon with inheritance
     */
    public function getFavicon(UnitType $unitType, ?int $unitId = null): ?string
    {
        return $this->settingService->get('favicon', $unitType, $unitId);
    }

    /**
     * Generate CSS variables from theme config
     */
    public function generateCssVariables(UnitType $unitType, ?int $unitId = null): string
    {
        $colors = $this->getColors($unitType, $unitId);
        $fonts = $this->getFonts($unitType, $unitId);

        $css = ":root {\n";
        
        // Colors
        $css .= "  --color-primary: {$colors['primary']};\n";
        $css .= "  --color-secondary: {$colors['secondary']};\n";
        $css .= "  --color-accent: {$colors['accent']};\n";
        
        // RGB versions for rgba usage
        $css .= "  --color-primary-rgb: " . $this->hexToRgb($colors['primary']) . ";\n";
        $css .= "  --color-secondary-rgb: " . $this->hexToRgb($colors['secondary']) . ";\n";
        $css .= "  --color-accent-rgb: " . $this->hexToRgb($colors['accent']) . ";\n";
        
        // Fonts
        $css .= "  --font-heading: '{$fonts['heading']}', sans-serif;\n";
        $css .= "  --font-body: '{$fonts['body']}', sans-serif;\n";
        
        $css .= "}\n";

        return $css;
    }

    /**
     * Convert hex color to RGB string
     */
    protected function hexToRgb(string $hex): string
    {
        $hex = ltrim($hex, '#');
        
        if (strlen($hex) === 3) {
            $hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
        }
        
        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));
        
        return "{$r}, {$g}, {$b}";
    }

    /**
     * Generate Google Fonts link
     */
    public function generateGoogleFontsLink(UnitType $unitType, ?int $unitId = null): string
    {
        $fonts = $this->getFonts($unitType, $unitId);
        $fontFamilies = array_unique(array_filter([$fonts['heading'], $fonts['body']]));
        
        if (empty($fontFamilies)) {
            return '';
        }

        $families = [];
        foreach ($fontFamilies as $font) {
            $families[] = urlencode($font) . ':wght@400;500;600;700';
        }

        return 'https://fonts.googleapis.com/css2?family=' . implode('&family=', $families) . '&display=swap';
    }

    /**
     * Clear theme cache
     */
    public function clearCache(UnitType $unitType, ?int $unitId = null): void
    {
        Cache::forget("theme.{$unitType->value}.{$unitId}");
    }

    /**
     * Clear all theme caches
     */
    public function clearAllCache(): void
    {
        Cache::flush();
    }
}
