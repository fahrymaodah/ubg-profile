<?php

use App\Enums\UnitType;
use App\Services\MenuService;
use App\Services\SettingService;
use App\Services\ThemeService;
use App\Services\UnitService;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Register middleware aliases
        $middleware->alias([
            'resolve.unit' => \App\Http\Middleware\ResolveUnit::class,
            'unit.published' => \App\Http\Middleware\CheckUnitPublished::class,
            'unit.context' => \App\Http\Middleware\SetUnitContext::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Render error pages with unit context
        $exceptions->render(function (HttpExceptionInterface $e, $request) {
            $statusCode = $e->getStatusCode();
            
            // Only handle specific error codes that have custom views
            if (!in_array($statusCode, [403, 404, 500])) {
                return null;
            }
            
            // Resolve unit from request
            $unitService = app(UnitService::class);
            $unitType = UnitType::UNIVERSITAS;
            $unitId = null;
            $unit = null;
            
            // Try to resolve unit from subdomain
            $host = $request->getHost();
            $mainDomain = config('app.domain', 'ubg.ac.id');
            $host = preg_replace('/^www\./', '', $host);
            
            if (str_ends_with($host, '.' . $mainDomain)) {
                $subdomain = str_replace('.' . $mainDomain, '', $host);
                if ($subdomain !== 'admin') {
                    $result = $unitService->getUnitBySubdomain($subdomain);
                    if ($result['unit']) {
                        $unitType = $result['type'];
                        $unitId = $result['unit']->id;
                        $unit = $result['unit'];
                    }
                }
            }
            
            // Load context for error page
            $menuService = app(MenuService::class);
            $settingService = app(SettingService::class);
            $themeService = app(ThemeService::class);
            
            $menu = $menuService->getMenuTree($unitType, $unitId);
            $settings = $settingService->getAllForUnit($unitType, $unitId);
            $logos = [
                'logo' => $settingService->getCascadingLogo('logo', $unitType, $unitId, $unit),
                'logo_dark' => $settingService->getCascadingLogo('logo_dark', $unitType, $unitId, $unit),
                'favicon' => $settingService->getCascadingLogo('favicon', $unitType, $unitId, $unit),
            ];
            $theme = $themeService->getThemeConfig($unitType, $unitId);
            $cssVariables = $themeService->generateCssVariables($unitType, $unitId);
            
            return response()->view("errors.{$statusCode}", [
                'exception' => $e,
                'mainMenu' => $menu,
                'settings' => $settings,
                'logos' => $logos,
                'theme' => $theme,
                'cssVariables' => $cssVariables,
                'currentUnitType' => $unitType,
                'currentUnitId' => $unitId,
                'currentUnit' => $unit,
            ], $statusCode);
        });
    })->create();
