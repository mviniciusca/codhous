<?php

namespace App\Providers\Filament;

use App\Filament\Resources\BudgetResource;
use App\Filament\Resources\CustomerResource;
use App\Filament\Resources\ProductResource;
use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationItem;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Z3d0X\FilamentFabricator\FilamentFabricatorPlugin;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->breadcrumbs(false)
            ->maxContentWidth('full')
            ->id('admin')
            ->path('admin')
            ->navigationGroups([
                __('Budget'),
                __('Customers'),
                __('Communication'),
                'Settings',
            ])
            ->resources([
                config('filament-logger.activity_resource'),
            ])
            ->navigationItems([
                /** Budgets */
                NavigationItem::make('budget')
                    ->url(fn(): string => BudgetResource::getUrl())
                    ->label(fn(): string => __('Budgets'))
                    ->icon('heroicon-o-currency-dollar')
                    ->badge(BudgetResource::getNavigationBadge())
                    ->group(__('Budget'))
                    ->sort(1)
                    ->isActiveWhen(fn(): bool => request()->routeIs('filament.admin.resources.budgets.index')),
                NavigationItem::make('product')
                    ->url(fn(): string => ProductResource::getUrl())
                    ->label(fn(): string => __('Products'))
                    ->icon('heroicon-o-shopping-bag')
                    ->badge(ProductResource::count())
                    ->group(__('Budget'))
                    ->sort(2)
                    ->isActiveWhen(fn(): bool => request()->routeIs('filament.admin.resources.products.index')),
                NavigationItem::make('budget_settings')

                    ->label(fn(): string => __('Budget Settings'))
                    ->icon('heroicon-o-cog')
                    ->group(__('Budget'))
                    ->sort(3),
                /** Customers */
                NavigationItem::make('customer')
                    ->label(fn(): string => __('Customers'))
                    ->url(fn(): string => CustomerResource::getUrl())
                    ->badge(CustomerResource::count())
                    ->isActiveWhen(fn(): bool => request()->routeIs('filament.admin.resources.customers.index'))
                    ->icon('heroicon-o-users')
                    ->group(__('Customers'))
                    ->sort(1),
            ])
            ->plugins([
                FilamentShieldPlugin::make(),
                FilamentFabricatorPlugin::make(),
            ])
            ->login()
            ->colors([
                'danger'  => Color::Rose,
                'gray'    => Color::Zinc,
                'info'    => Color::Blue,
                'primary' => Color::Indigo,
                'success' => Color::Emerald,
                'warning' => Color::Orange,

            ])

            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                // Widgets\AccountWidget::class,
                // Widgets\FilamentInfoWidget::class,
            ])
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
