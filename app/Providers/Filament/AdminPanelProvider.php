<?php

namespace App\Providers\Filament;

use Filament\Pages;
use Filament\Panel;
use Filament\Widgets;
use App\Models\Setting;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Navigation\NavigationItem;
use App\Filament\Resources\MailResource;
use App\Filament\Resources\BudgetResource;
use Filament\Http\Middleware\Authenticate;
use App\Filament\Resources\ProductResource;
use App\Filament\Resources\CustomerResource;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Routing\Middleware\SubstituteBindings;
use App\Filament\Resources\MailResource\Pages\BinMail;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Z3d0X\FilamentFabricator\FilamentFabricatorPlugin;
use App\Filament\Resources\MailResource\Pages\ReadMail;
use App\Filament\Resources\MailResource\Pages\SentMail;
use App\Filament\Resources\MailResource\Pages\SpamMail;
use Filament\Http\Middleware\DisableBladeIconComponents;
use App\Filament\Resources\BudgetResource\Pages\BudgetBin;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use App\Filament\Resources\MailResource\Pages\FavoriteMail;
use App\Filament\Resources\SettingResource\Pages\EditBudget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use App\Filament\Resources\SettingResource\Pages\EditSetting;
use App\Filament\Resources\CustomerResource\Pages\CustomerBin;
use App\Filament\Resources\NewsletterResource\Pages\SubscriberBin;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->breadcrumbs(false)
            ->id('admin')
            ->path('admin')
            ->navigationGroups([
                __('Budget'),
                __('Mail'),
                __('Customers & Partners'),
                __('Mailing'),
                __('Settings'),
            ])
            ->resources([
                config('filament-logger.activity_resource')
            ])
            ->navigationItems([
                /** Budgets */
                // NavigationItem::make('budget')
                //     ->url(fn(): string => BudgetResource::getUrl())
                //     ->label(fn(): string => __('Budgets'))
                //     ->icon('heroicon-o-currency-dollar')
                //     ->badge(fn(): ?string => BudgetResource::getNavigationBadge())
                //     ->group(__('Budget'))
                //     ->sort(1)
                //     ->isActiveWhen(fn(): bool => request()->routeIs('filament.admin.resources.budgets.index')),
                // NavigationItem::make('product')
                //     ->url(fn(): string => ProductResource::getUrl())
                //     ->label(fn(): string => __('Products'))
                //     ->icon('heroicon-o-shopping-bag')
                //     ->badge(fn(): ?string => ProductResource::getNavigationBadge())
                //     ->group(__('Budget'))
                //     ->sort(2)
                //     ->badge(fn(): ?string => ProductResource::count())
                //     ->isActiveWhen(fn(): bool => request()->routeIs('filament.admin.resources.products.index')),
                // NavigationItem::make('budget_bin')
                //     ->label(fn(): string => __('Trash'))
                //     ->url(fn(): string => BudgetBin::getUrl())
                //     ->badge(fn(): ?string => BudgetBin::count())
                //     ->icon('heroicon-o-trash')
                //     ->group(__('Budget'))
                //     ->isActiveWhen(fn(): bool => request()->routeIs('filament.admin.resources.budgets.bin'))
                //     ->sort(4),
                // NavigationItem::make('budget')
                //     ->url(fn(): string => EditBudget::getUrl([Setting::first()->id]), )
                //     ->label(fn(): string => __('Budget Tool'))
                //     ->icon('heroicon-o-arrow-up-right')
                //     ->group(__('Budget'))
                //     ->sort(3),
                /** Inbox */
                // NavigationItem::make('inbox')
                //     ->label(fn(): string => __('Inbox'))
                //     ->badge(fn(): ?string => MailResource::count())
                //     ->url(fn(): string => MailResource::getUrl())
                //     ->isActiveWhen(fn(): bool => request()->routeIs('filament.admin.resources.mails.index'))
                //     ->icon('heroicon-o-envelope')
                //     ->group(__('Mail'))
                //     ->sort(1),
                // NavigationItem::make('read')
                //     ->label(fn(): string => __('Read'))
                //     ->badge(fn(): ?string => ReadMail::count())
                //     ->url(fn(): string => ReadMail::getUrl())
                //     ->isActiveWhen(fn(): bool => request()->routeIs('filament.admin.resources.mails.read'))
                //     ->icon('heroicon-o-envelope-open')
                //     ->group(__('Mail'))
                //     ->sort(1),
                // NavigationItem::make('sent')
                //     ->label(fn(): string => __('Sent'))
                //     ->url(fn(): string => SentMail::getUrl())
                //     ->badge(fn(): ?string => SentMail::count())
                //     ->isActiveWhen(fn(): bool => request()->routeIs('filament.admin.resources.mails.sent'))
                //     ->icon('heroicon-o-paper-airplane')
                //     ->group(__('Mail'))
                //     ->sort(2),
                // NavigationItem::make('fav')
                //     ->label(fn(): string => __('Important'))
                //     ->url(fn(): string => FavoriteMail::getUrl())
                //     ->badge(fn(): ?string => FavoriteMail::count())
                //     ->isActiveWhen(fn(): bool => request()->routeIs('filament.admin.resources.mails.fav'))
                //     ->icon('heroicon-o-heart')
                //     ->group(__('Mail'))
                //     ->sort(2),
                // NavigationItem::make('spam')
                //     ->label(fn(): string => __('Spam'))
                //     ->url(fn(): string => SpamMail::getUrl())
                //     ->badge(fn(): ?string => SpamMail::count())
                //     ->isActiveWhen(fn(): bool => request()->routeIs('filament.admin.resources.mails.spam'))
                //     ->icon('heroicon-o-flag')
                //     ->group(__('Mail'))
                //     ->sort(3),
                // NavigationItem::make('bin')
                //     ->label(fn(): string => __('Trash'))
                //     ->url(fn(): string => BinMail::getUrl())
                //     ->badge(fn(): ?string => BinMail::count())
                //     ->isActiveWhen(fn(): bool => request()->routeIs('filament.admin.resources.mails.bin'))
                //     ->icon('heroicon-o-trash')
                //     ->group(__('Mail'))
                //     ->sort(4),
                /** Customers */
                // NavigationItem::make('customer')
                //     ->label(fn(): string => __('Customers'))
                //     ->url(fn(): string => CustomerResource::getUrl())
                //     ->badge(fn(): ?string => CustomerResource::count())
                //     ->isActiveWhen(fn(): bool => request()->routeIs('filament.admin.resources.customers.index'))
                //     ->icon('heroicon-o-user')
                //     ->group(__('Customers & Partners'))
                //     ->sort(1),
                // NavigationItem::make('customer_bin')
                //     ->label(fn(): string => __('Trash'))
                //     ->url(fn(): string => CustomerBin::getUrl())
                //     ->badge(fn(): ?string => CustomerBin::count())
                //     ->icon('heroicon-o-trash')
                //     ->isActiveWhen(fn(): bool => request()->routeIs('filament.admin.resources.customers.bin'))
                //     ->group(__('Customers & Partners'))
                //     ->sort(2),
                // /** Mailing List */
                // NavigationItem::make('subscriber_bin')
                //     ->label(fn(): string => __('Trash'))
                //     ->url(fn(): string => SubscriberBin::getUrl())
                //     ->badge(fn(): ?string => SubscriberBin::count())
                //     ->icon('heroicon-o-trash')
                //     ->group(__('Mailing'))
                //     ->sort(2),
                // /** App Setting */
                // NavigationItem::make('settings')
                //     ->label(fn(): string => __('App Settings'))
                //     ->url(fn(): string => EditSetting::getUrl([1]))
                //     ->icon('heroicon-o-cog-6-tooth')
                //     ->group(__('Settings'))
                //     ->sort(1),
            ])
            ->plugins([
                FilamentFabricatorPlugin::make(),
            ])
            ->login()
            ->colors([
                'primary' => Color::Indigo,
                'info' => Color::Blue,
                'danger' => Color::Red,
                'success' => Color::Green,
                'secondary' => Color::Zinc,
                'tertiary' => Color::Rose,
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
                Widgets\FilamentInfoWidget::class,
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
