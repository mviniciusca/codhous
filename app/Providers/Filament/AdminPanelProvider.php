<?php

namespace App\Providers\Filament;

use App\Filament\Resources\BudgetResource;
use App\Filament\Resources\CustomerResource;
use App\Filament\Resources\ProductResource;
use App\Filament\Resources\SettingResource\Pages\EditBudget;
use App\Filament\Resources\SettingResource\Pages\EditSetting;
use App\Models\Setting;
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
                __('Budget Tool'),
                __('Mail'),
                __('Customers & Partners'),
                __('Mailing'),
                __('Settings'),
            ])
            ->resources([
                config('filament-logger.activity_resource'),
            ])
            ->navigationItems([
                /** Budgets */
                NavigationItem::make('budget')
                    ->url(fn (): string => BudgetResource::getUrl())
                    ->label(fn (): string => __('Budgets'))
                    ->icon('heroicon-o-currency-dollar')
                    ->badge(fn (): ?string => BudgetResource::getNavigationBadge())
                    ->group(__('Budget'))
                    ->sort(1)
                    ->isActiveWhen(fn (): bool => request()->routeIs('filament.admin.resources.budgets.index')),
                NavigationItem::make('product')
                    ->url(fn (): string => ProductResource::getUrl())
                    ->label(fn (): string => __('Products'))
                    ->icon('heroicon-o-shopping-bag')
                    ->badge(fn (): ?string => ProductResource::getNavigationBadge())
                    ->group(__('Budget Tool'))
                    ->sort(2)
                    ->badge(fn (): ?string => ProductResource::count())
                    ->isActiveWhen(fn (): bool => request()->routeIs('filament.admin.resources.products.index')),

                NavigationItem::make('budget')
                    ->url(fn (): string => EditBudget::getUrl([Setting::first()->id]), )
                    ->label(fn (): string => __('Design & Layout'))
                    ->icon('heroicon-o-arrow-up-right')
                    ->group(__('Budget Tool'))
                    ->sort(3),
                /** Customers */
                NavigationItem::make('customer')
                    ->label(fn (): string => __('Customers'))
                    ->url(fn (): string => CustomerResource::getUrl())
                    ->badge(fn (): ?string => CustomerResource::count())
                    ->isActiveWhen(fn (): bool => request()->routeIs('filament.admin.resources.customers.index'))
                    ->icon('heroicon-o-user')
                    ->group(__('Customers & Partners'))
                    ->sort(1),

                // /** App Setting */
                NavigationItem::make('settings')
                    ->label(fn (): string => __('App Settings'))
                    ->url(fn (): string => EditSetting::getUrl([1]))
                    ->icon('heroicon-o-cog-6-tooth')
                    ->group(__('Settings'))
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
                'primary' => Color::Zinc,
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
