<?php

namespace App\Providers;

use App\Models\Setting;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::share('discovery_mode', Setting::select(['discovery_mode'])->first()->discovery_mode);
        View::share('maintenance_mode', Setting::select(['maintenance_mode'])->first()->maintenance_mode);
    }
}
