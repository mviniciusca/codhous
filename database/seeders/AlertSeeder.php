<?php

namespace Database\Seeders;

use App\Models\Alert;
use Illuminate\Database\Seeder;

class AlertSeeder extends Seeder
{
    public function run(): void
    {
        $alerts = [
            // Aviso de cookies (LGPD): banner no rodapé, gerenciado em Alertas e notificações
            Alert::cookieConsentDefaults(),
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
