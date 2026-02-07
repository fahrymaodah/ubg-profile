<?php

namespace App\Providers\Filament;

use App\Filament\Widgets\LatestArticlesWidget;
use App\Filament\Widgets\StatsOverviewWidget;
use App\Services\AppConfigService;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Support\Facades\FilamentView;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Blade;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->viteTheme('resources/css/filament/admin/theme.css')
            ->login()
            ->brandName('UBG Admin')
            ->brandLogo(asset('images/logo-ubg-label.png'))
            ->darkModeBrandLogo(asset('images/logo-ubg-label-white.png'))
            ->brandLogoHeight('3rem')
            ->favicon(asset('images/logo-ubg-white.png'))
            ->colors([
                'primary' => Color::Blue,
                'danger' => Color::Red,
                'gray' => Color::Slate,
                'info' => Color::Sky,
                'success' => Color::Emerald,
                'warning' => Color::Amber,
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                // Widgets\AccountWidget::class,
                StatsOverviewWidget::class,
                LatestArticlesWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            ->sidebarCollapsibleOnDesktop()
            ->maxContentWidth('full')
            ->globalSearchKeyBindings(['command+k', 'ctrl+k'])
            ->renderHook(
                'panels::body.start',
                fn () => view('filament.components.system-notice')
            )
            // Manual Livewire loading - Cloudflare bypass (blocks ?id=xxx query string)
            ->renderHook(
                'panels::head.end',
                fn () => view('filament.components.livewire-styles')
            )
            // Load Livewire BEFORE Filament's app.js (so Alpine is available)
            ->renderHook(
                'panels::scripts.before',
                fn () => view('filament.components.livewire-scripts')
            )
            // Start Livewire AFTER Filament's app.js (after Alpine components are registered)
            ->renderHook(
                'panels::scripts.after',
                fn () => view('filament.components.livewire-start')
            );
    }
}
