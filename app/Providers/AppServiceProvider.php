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
                $menuService = app(MenuService::class);
                $settingService = app(SettingService::class);
                $themeService = app(ThemeService::class);

                // Default to universitas context
                $unitType = UnitType::UNIVERSITAS;
                $unitId = null;

                $view->with([
                    'mainMenu' => $menuService->getMenuTree($unitType, $unitId),
                    'settings' => $settingService->getAllForUnit($unitType, $unitId),
                    'theme' => $themeService->getThemeConfig($unitType, $unitId),
                    'unitHierarchy' => [],
                    'cssVariables' => $themeService->generateCssVariables($unitType, $unitId),
                    'fontsLink' => $themeService->generateGoogleFontsLink($unitType, $unitId),
                    'customCss' => $themeService->getCustomCss($unitType, $unitId),
                    'customJs' => $themeService->getCustomJs($unitType, $unitId),
                ]);
            }
        });
    }
}
