<?php

namespace Database\Seeders;

use App\Models\Alert;
use Illuminate\Database\Seeder;

class AlertSeeder extends Seeder
{
    public function run(): void
    {
        $alerts = [
            [
                'name' => 'Consentimento de cookies',
                'type' => Alert::TYPE_BANNER,
                'style' => Alert::STYLE_CONSENT,
                'title' => 'Este site usa cookies',
                'message' => 'Utilizamos cookies para melhorar sua experiência e analisar o tráfego. Ao continuar navegando, você concorda com nossa política de privacidade.',
                'position' => Alert::POSITION_BOTTOM,
                'is_active' => true,
                'is_dismissible' => true,
                'use_cookie' => true,
                'cookie_key' => 'cookie_consent_accepted',
                'cookie_duration_days' => 365,
                'cta_label' => 'Política de privacidade',
                'cta_url' => '/politica-de-privacidade',
                'sort_order' => 0,
            ],
            [
                'name' => 'Banner informativo (exemplo)',
                'type' => Alert::TYPE_BANNER,
                'style' => Alert::STYLE_INFO,
                'title' => 'Atendimento',
                'message' => 'Atendemos Rio de Janeiro e Grande Rio. Solicite seu orçamento!',
                'position' => Alert::POSITION_TOP,
                'is_active' => false,
                'is_dismissible' => true,
                'use_cookie' => false,
                'sort_order' => 1,
            ],
        ];

        foreach ($alerts as $data) {
            Alert::updateOrCreate(
                ['name' => $data['name']],
                $data
            );
        }
    }
}
