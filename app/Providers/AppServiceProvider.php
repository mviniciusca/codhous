<?php

namespace App\Providers;

use App\Models\Budget;
use App\Models\Customer;
use App\Models\Mail;
use App\Models\Setting;
use App\Observers\BudgetObserver;
use App\Policies\BudgetPolicy;
use App\Policies\CustomerPolicy;
use App\Policies\MailPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Budget::class   => BudgetPolicy::class,
        Mail::class     => MailPolicy::class,
        Customer::class => CustomerPolicy::class,
    ];

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
        // Register observers
        Budget::observe(BudgetObserver::class);

        // Register policies
        foreach ($this->policies as $model => $policy) {
            Gate::policy($model, $policy);
        }

        // Verifica se a tabela settings existe (para evitar erros durante migrações)
        if (Schema::hasTable('settings')) {
            // Para configurações críticas como modo de manutenção, verificamos diretamente
            $maintenanceMode = false;
            $discoveryMode = false;

            try {
                $settings = Setting::select(['maintenance_mode', 'discovery_mode'])->first();
                if ($settings) {
                    $maintenanceMode = $settings->maintenance_mode ?? false;
                    $discoveryMode = $settings->discovery_mode ?? false;
                }
            } catch (\Exception $e) {
                // Falha silenciosa - pode ocorrer durante migrações
            }

            // Compartilha as configurações com todas as views
            View::share('maintenance_mode', $maintenanceMode);
            View::share('discovery_mode', $discoveryMode);

            // Define configuração em nível de aplicação para o middleware usar
            if ($maintenanceMode) {
                config(['app.maintenance' => true]);
            }

            // Outras configurações não críticas podem usar cache normalmente
            // cache()->remember('other_settings', 60 * 60, function () {
            //     return Setting::select(['other_setting1', 'other_setting2'])->first();
            // });
        }
    }
}
