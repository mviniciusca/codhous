<?php

namespace App\Providers;

use App\Models\Setting;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Schema;
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

        if (Schema::hasColumn('settings', 'discovery_mode')) {
            View::share('discovery_mode', Setting::select(['discovery_mode'])->first()->discovery_mode);
        }

        if (Schema::hasColumn('settings', 'maintenance_mode')) {
            View::share('maintenance_mode', Setting::select(['maintenance_mode'])->first()->maintenance_mode);
        }
    }
}
