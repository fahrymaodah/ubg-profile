<?php

namespace App\Providers;

use App\Models\Article;
use App\Models\Dosen;
use App\Models\Fakultas;
use App\Models\Menu;
use App\Models\Prodi;
use App\Models\Setting;
use App\Models\User;
use App\Observers\FakultasObserver;
use App\Observers\ProdiObserver;
use App\Policies\ArticlePolicy;
use App\Policies\DosenPolicy;
use App\Policies\FakultasPolicy;
use App\Policies\MenuPolicy;
use App\Policies\ProdiPolicy;
use App\Policies\SettingPolicy;
use App\Policies\UserPolicy;
use App\Services\MenuService;
use App\Services\SettingService;
use App\Services\ThemeService;
use App\Services\UnitService;
use App\Enums\UnitType;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     */
    protected array $policies = [
        User::class => UserPolicy::class,
        Article::class => ArticlePolicy::class,
        Menu::class => MenuPolicy::class,
        Fakultas::class => FakultasPolicy::class,
        Prodi::class => ProdiPolicy::class,
        Setting::class => SettingPolicy::class,
        Dosen::class => DosenPolicy::class,
    ];

    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Register services as singletons
        $this->app->singleton(UnitService::class);
        $this->app->singleton(MenuService::class);
        $this->app->singleton(SettingService::class);
        $this->app->singleton(ThemeService::class, function ($app) {
            return new ThemeService($app->make(SettingService::class));
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register model observers
        Fakultas::observe(FakultasObserver::class);
        Prodi::observe(ProdiObserver::class);

        // Register policies
        foreach ($this->policies as $model => $policy) {
            Gate::policy($model, $policy);
        }

        // Share Setting model to all views
        View::share('Setting', Setting::class);

        // View composer for error pages and views without middleware context
        View::composer('errors::*', function ($view) {
            // Only set if not already set by middleware
            if (!$view->offsetExists('mainMenu')) {
                try {
                    $menuService = app(MenuService::class);
                    $settingService = app(SettingService::class);
                    $themeService = app(ThemeService::class);
                    $unitService = app(UnitService::class);

                    // Try to detect unit from current request or view context
                    $unitType = $view->offsetExists('currentUnitType') 
                        ? $view->offsetGet('currentUnitType') 
                        : null;
                    $unitId = $view->offsetExists('currentUnitId') 
                        ? $view->offsetGet('currentUnitId') 
                        : null;
                    $unit = $view->offsetExists('currentUnit') 
                        ? $view->offsetGet('currentUnit') 
                        : null;

                    // If not set by middleware/view, try to resolve from request
                    if ($unitType === null) {
                        $request = request();
                        $host = $request->getHost();
                        $mainDomain = config('app.domain', 'ubg.ac.id');
                        
                        // Remove www if present
                        $host = preg_replace('/^www\./', '', $host);
                        
                        // Check if this is a subdomain
                        if ($host === $mainDomain || $host === 'localhost' || str_starts_with($host, '127.0.0.1')) {
                            // Main domain - universitas level
                            $unitType = UnitType::UNIVERSITAS;
                            $unitId = null;
                        } elseif (str_ends_with($host, '.' . $mainDomain)) {
                            // Subdomain - resolve unit
                            $subdomain = str_replace('.' . $mainDomain, '', $host);
                            $subdomain = explode(':', $subdomain)[0]; // Remove port for local dev
                            
                            // Skip admin and profil subdomains (handled by Filament)
                            if (!in_array($subdomain, ['admin', 'profil'])) {
                                $result = $unitService->getUnitBySubdomain($subdomain);
                                
                                if ($result['unit']) {
                                    $unitType = $result['type'];
                                    $unitId = $result['unit']->id;
                                    $unit = $result['unit'];
                                } else {
                                    // Subdomain not found, default to universitas
                                    $unitType = UnitType::UNIVERSITAS;
                                    $unitId = null;
                                }
                            } else {
                                // Admin/profil subdomain, use universitas
                                $unitType = UnitType::UNIVERSITAS;
                                $unitId = null;
                            }
                        } else {
                            // Unknown host, default to universitas
                            $unitType = UnitType::UNIVERSITAS;
                            $unitId = null;
                        }
                    }

                    // Load data for the detected unit context
                    $view->with([
                        'mainMenu' => $menuService->getMenuTree($unitType, $unitId),
                        'settings' => $settingService->getAllForUnit($unitType, $unitId),
                        'theme' => $themeService->getThemeConfig($unitType, $unitId),
                        'unitHierarchy' => $unitService->getUnitHierarchy($unitType, $unitId),
                        'cssVariables' => $themeService->generateCssVariables($unitType, $unitId),
                        'fontsLink' => $themeService->generateGoogleFontsLink($unitType, $unitId),
                        'customCss' => $themeService->getCustomCss($unitType, $unitId),
                        'customJs' => $themeService->getCustomJs($unitType, $unitId),
                        'logos' => [
                            'logo' => $settingService->getCascadingLogo('logo', $unitType, $unitId, $unit),
                            'logo_dark' => $settingService->getCascadingLogo('logo_dark', $unitType, $unitId, $unit),
                            'favicon' => $settingService->getCascadingLogo('favicon', $unitType, $unitId, $unit),
                        ],
                        'currentUnitType' => $unitType,
                        'currentUnitId' => $unitId,
                        'currentUnit' => $unit,
                    ]);
                } catch (\Throwable $e) {
                    // If something goes wrong (database error, etc), use safe defaults
                    $view->with([
                        'mainMenu' => [],
                        'settings' => [
                            'site_name' => config('app.name', 'UBG'),
                            'email' => 'info@ubg.ac.id',
                        ],
                        'theme' => [],
                        'unitHierarchy' => [],
                        'cssVariables' => '',
                        'fontsLink' => '',
                        'customCss' => '',
                        'customJs' => '',
                        'logos' => [
                            'logo' => asset('images/logo-ubg.png'),
                            'logo_dark' => asset('images/logo-ubg.png'),
                            'favicon' => asset('images/logo-ubg.png'),
                        ],
                        'currentUnitType' => UnitType::UNIVERSITAS,
                        'currentUnitId' => null,
                        'currentUnit' => null,
                    ]);
                }
            }
        });
    }
}
