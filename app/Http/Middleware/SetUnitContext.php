<?php

namespace App\Http\Middleware;

use App\Enums\UnitType;
use App\Services\MenuService;
use App\Services\SettingService;
use App\Services\ThemeService;
use App\Services\UnitService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetUnitContext
{
    public function __construct(
        protected UnitService $unitService,
        protected MenuService $menuService,
        protected SettingService $settingService,
        protected ThemeService $themeService
    ) {}

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $unitType = $request->attributes->get('unit_type');
        $unitId = $request->attributes->get('unit_id');
        $unit = $request->attributes->get('unit');

        // Skip if unit_type is not set (e.g., profil subdomain handled by Filament)
        if ($unitType === null) {
            return $next($request);
        }

        try {
            // Load and share common data with views

            // Navigation menu
            $menu = $this->menuService->getMenuTree($unitType, $unitId);
            view()->share('mainMenu', $menu);

            // Settings
            $settings = $this->settingService->getAllForUnit($unitType, $unitId);
            view()->share('settings', $settings);

            // Cascading logos (prodi → fakultas → universitas → default)
            $logos = [
                'logo' => $this->settingService->getCascadingLogo('logo', $unitType, $unitId, $unit),
                'logo_dark' => $this->settingService->getCascadingLogo('logo_dark', $unitType, $unitId, $unit),
                'favicon' => $this->settingService->getCascadingLogo('favicon', $unitType, $unitId, $unit),
            ];
            view()->share('logos', $logos);

            // Theme
            $theme = $this->themeService->getThemeConfig($unitType, $unitId);
            view()->share('theme', $theme);

            // Unit hierarchy (for breadcrumbs)
            $hierarchy = $this->unitService->getUnitHierarchy($unitType, $unitId);
            view()->share('unitHierarchy', $hierarchy);

            // CSS variables
            $cssVariables = $this->themeService->generateCssVariables($unitType, $unitId);
            view()->share('cssVariables', $cssVariables);

            // Google Fonts link
            $fontsLink = $this->themeService->generateGoogleFontsLink($unitType, $unitId);
            view()->share('fontsLink', $fontsLink);

            // Custom CSS and JS
            view()->share('customCss', $this->themeService->getCustomCss($unitType, $unitId));
            view()->share('customJs', $this->themeService->getCustomJs($unitType, $unitId));

            // For prodi, also share parent fakultas info
            if ($unitType === UnitType::PRODI && $unit) {
                view()->share('fakultas', $unit->fakultas);
            }

            // For fakultas, share list of prodi
            if ($unitType === UnitType::FAKULTAS && $unit) {
                view()->share('prodiList', $this->unitService->getProdiByFakultas($unit->id));
            }

            // Share profil data (visi, misi, sejarah, struktur)
            $profil = $this->settingService->getProfilData($unitType, $unitId, $unit);
            view()->share('profil', $profil);

        } catch (\Throwable $e) {
            // Log error with context
            \Log::error('SetUnitContext middleware error', [
                'unit_type' => $unitType?->value,
                'unit_id' => $unitId,
                'unit_name' => $unit?->nama ?? 'N/A',
                'subdomain' => $request->getHost(),
                'path' => $request->path(),
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            // Re-throw to show proper error page
            throw $e;
        }

        return $next($request);
    }
}
