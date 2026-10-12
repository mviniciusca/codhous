<?php

use App\Models\Alert;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * O aviso de cookies deixou de ser um componente fixo do site (site-cookie-consent)
 * e passou a ser um alerta. Instalações existentes recebem o alerta aqui, respeitando
 * a antiga opção "Aviso de Cookies" (security.cookie_consent_enabled).
 * Instalações novas (sem configurações ainda) recebem o alerta pelo AlertSeeder.
 */
return new class extends Migration
{
    public function up(): void
    {
        $settings = DB::table('settings')->value('settings');

        if ($settings === null || DB::table('alerts')->where('style', Alert::STYLE_CONSENT)->exists()) {
            return;
        }

        $enabled = data_get(json_decode((string) $settings, true), 'security.cookie_consent_enabled', true);

        DB::table('alerts')->insert([
            ...Alert::cookieConsentDefaults(),
            'is_active' => (bool) $enabled,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        DB::table('alerts')
            ->where('name', Alert::COOKIE_CONSENT_NAME)
            ->where('cookie_key', 'codhous_cookie_consent')
            ->delete();
    }
};
