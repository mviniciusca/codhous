<?php

namespace App\Providers\Filament;

use App\Filament\Pages\Auth\EditProfile;
use App\Filament\Pages\Dashboard;
use App\Filament\Resources\BudgetResource\Pages\CreateBudget;
use App\Filament\Resources\BudgetResource\Pages\EditBudget;
use Filament\Enums\ThemeMode;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationItem;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\Platform;
use Filament\View\PanelsRenderHook;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Pboivin\FilamentPeek\FilamentPeekPlugin;

class AdminPanelProvider extends PanelProvider
{
    /** Neutros em grafite levemente frio: superfícies do modo escuro (900/950) e textos */
    public const GRAPHITE = [
        50 => '246, 247, 249',
        100 => '236, 238, 242',
        200 => '214, 218, 225',
        300 => '179, 186, 198',
        400 => '138, 147, 163',
        500 => '107, 116, 132',
        600 => '84, 91, 105',
        700 => '63, 69, 80',
        800 => '39, 43, 51',
        900 => '23, 26, 32',
        950 => '13, 15, 19',
    ];

    /** Azul elétrico da marca */
    public const ELECTRIC_BLUE = [
        50 => '238, 244, 255',
        100 => '219, 231, 255',
        200 => '191, 212, 255',
        300 => '147, 182, 255',
        400 => '96, 141, 255',
        500 => '54, 104, 255',
        600 => '35, 80, 245',
        700 => '28, 63, 224',
        800 => '30, 54, 181',
        900 => '30, 51, 143',
        950 => '23, 33, 87',
    ];

    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->sidebarCollapsibleOnDesktop()
            ->breadcrumbs(true)
            ->maxContentWidth('full')
            ->id('admin')
            ->path('admin')
            ->navigationGroups([
                __('Orçamentos'),
                __('Website'),
                __('Empresa'),
                __('Configurações'),
            ])
            ->resources([
                config('filament-logger.activity_resource'),
            ])
            ->plugins([
                FilamentPeekPlugin::make(),
            ])
            ->renderHook(
                PanelsRenderHook::HEAD_END,
                fn () => view('filament.admin.theme'),
            )
            ->globalSearchKeyBindings(['command+k', 'ctrl+k'])
            ->globalSearchFieldSuffix(fn (): ?string => match (Platform::detect()) {
                Platform::Mac => '⌘K',
                Platform::Windows, Platform::Linux => 'Ctrl K',
                default => null,
            })
            ->renderHook(
                PanelsRenderHook::GLOBAL_SEARCH_AFTER,
                fn () => view('filament.admin.topbar-actions'),
            )
            ->renderHook(
                PanelsRenderHook::BODY_END,
                fn () => auth()->check() ? view('filament.admin.profile-modal-host') : '',
            )
            ->renderHook(
                PanelsRenderHook::HEAD_END,
                fn () => view('filament.budget.styles'),
                scopes: [
                    EditBudget::class,
                    CreateBudget::class,
                ],
            )
            ->login()
            ->passwordReset()
            ->profile(EditProfile::class)
            ->font('Jost')
            ->defaultThemeMode(ThemeMode::Dark)
            ->colors([
                'danger' => Color::Red,
                'gray' => self::GRAPHITE,
                'info' => Color::Sky,
                'primary' => self::ELECTRIC_BLUE,
                'success' => Color::Emerald,
                'warning' => Color::Amber,
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->navigationItems([
                NavigationItem::make('Google Analytics')
                    ->url('https://analytics.google.com', shouldOpenInNewTab: true)
                    ->icon('heroicon-o-presentation-chart-line')
                    ->sort(2),
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->discoverClusters(in: app_path('Filament/Clusters'), for: 'App\\Filament\\Clusters')
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
            ]);
    }
}
