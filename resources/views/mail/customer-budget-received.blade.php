@php
    $content = $budget->content ?? [];
    $companyName = $company->trade_name ?? config('app.name');
    $companyPhone = $company->phone ?? null;
    $periods = \App\Livewire\Budget::DELIVERY_PERIODS;
    $pumpOptions = \App\Livewire\Budget::PUMP_OPTIONS;
    $deliveryDate = !empty($content['delivery_date']) ? \Illuminate\Support\Carbon::parse($content['delivery_date'])->format('d/m/Y') : null;
    $deliveryPeriod = $periods[$content['delivery_period'] ?? ''] ?? null;
@endphp
<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&display=swap" rel="stylesheet">
    <title>Recebemos sua solicitação</title>
    <style>
        body, table, td, a { -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%; }
        table, td { mso-table-lspace: 0pt; mso-table-rspace: 0pt; }
        table { border-collapse: collapse !important; }
        body { height: 100% !important; margin: 0 !important; padding: 0 !important; width: 100% !important; background-color: #f8fafc; }

        @media screen and (max-width: 600px) {
            .container { width: 100% !important; }
            .content { padding: 30px 20px !important; }
        }
    </style>
</head>
<body style="font-family: 'Inter', Helvetica, Arial, sans-serif; color: #1e293b;">
    <table border="0" cellpadding="0" cellspacing="0" width="100%">
        <tr>
            <td style="padding: 40px 20px;" align="center">
                <table border="0" cellpadding="0" cellspacing="0" width="600" class="container" style="background-color: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);">

                    <!-- Header -->
                    <tr>
                        <td style="background-color: #0f172a; padding: 40px; text-align: center;">
                            <span style="display: inline-block; background-color: #38bdf8; color: #0f172a; font-size: 12px; font-weight: 800; text-transform: uppercase; padding: 4px 12px; border-radius: 9999px; margin-bottom: 16px; letter-spacing: 1px;">Pedido #{{ $budget->code }}</span>
                            <h1 style="color: #ffffff; font-size: 26px; font-weight: 800; margin: 0; letter-spacing: -1px;">Recebemos sua solicitação!</h1>
                        </td>
                    </tr>

                    <!-- Body -->
                    <tr>
                        <td class="content" style="padding: 40px 50px;">
                            <p style="font-size: 16px; line-height: 1.6; color: #475569; margin: 0 0 24px;">
                                Olá, <strong>{{ explode(' ', trim($customerName))[0] }}</strong>! Obrigado por solicitar um orçamento à <strong>{{ $companyName }}</strong>.
                                Nossa equipe vai revisar seu pedido e enviar a proposta pelo WhatsApp ou e-mail em breve.
                            </p>

                            <!-- Itens -->
                            <table border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #f1f5f9; border-radius: 12px; margin-bottom: 24px;">
                                <tr>
                                    <td style="padding: 24px;">
                                        <span style="display: block; font-size: 11px; font-weight: 700; color: #94a3b8; text-transform: uppercase; margin-bottom: 12px;">Itens solicitados</span>
                                        @foreach ($budget->budgetItems as $item)
                                            <p style="margin: 0 0 8px; font-size: 15px; color: #1e293b;">
                                                <strong>{{ \App\Services\BudgetCalculatorService::formatQuantityWithUnit((float) $item->quantity, $item->unitLabel()) }}</strong>
                                                &mdash; {{ $item->productLabel() }}
                                                @if ($item->optionLabel())
                                                    <span style="color: #64748b;">({{ $item->optionLabel() }})</span>
                                                @endif
                                            </p>
                                        @endforeach
                                    </td>
                                </tr>
                            </table>

                            <!-- Local e entrega -->
                            <table border="0" cellpadding="0" cellspacing="0" width="100%" style="margin-bottom: 24px;">
                                <tr>
                                    <td style="padding: 0 0 16px; border-bottom: 1px solid #e2e8f0;">
                                        <span style="display: block; font-size: 11px; font-weight: 700; color: #94a3b8; text-transform: uppercase; margin-bottom: 4px;">Local da obra</span>
                                        <span style="font-size: 15px; color: #1e293b;">
                                            {{ $content['street'] ?? '' }}, {{ $content['number'] ?? '' }}@if (!empty($content['complement'])) &ndash; {{ $content['complement'] }}@endif<br>
                                            {{ $content['neighborhood'] ?? '' }} &ndash; {{ $content['city'] ?? '' }}/{{ $content['state'] ?? '' }} &middot; CEP {{ $content['postcode'] ?? '' }}
                                        </span>
                                    </td>
                                </tr>
                                @if ($deliveryDate || $deliveryPeriod)
                                    <tr>
                                        <td style="padding: 16px 0; border-bottom: 1px solid #e2e8f0;">
                                            <span style="display: block; font-size: 11px; font-weight: 700; color: #94a3b8; text-transform: uppercase; margin-bottom: 4px;">Entrega desejada</span>
                                            <span style="font-size: 15px; color: #1e293b;">{{ collect([$deliveryDate, $deliveryPeriod])->filter()->implode(' · ') }}</span>
                                        </td>
                                    </tr>
                                @endif
                                @if (!empty($content['needs_pump']))
                                    <tr>
                                        <td style="padding: 16px 0; border-bottom: 1px solid #e2e8f0;">
                                            <span style="display: block; font-size: 11px; font-weight: 700; color: #94a3b8; text-transform: uppercase; margin-bottom: 4px;">Bombeamento</span>
                                            <span style="font-size: 15px; color: #1e293b;">{{ $pumpOptions[$content['needs_pump']] ?? $content['needs_pump'] }}</span>
                                        </td>
                                    </tr>
                                @endif
                            </table>

                            <p style="font-size: 14px; line-height: 1.6; color: #64748b; margin: 0;">
                                Guarde o código <strong>#{{ $budget->code }}</strong> para falar com a gente sobre este pedido.
                                @if ($companyPhone)
                                    Se preferir, fale agora pelo WhatsApp:
                                    <a href="https://wa.me/55{{ preg_replace('/\D/', '', $companyPhone) }}" style="color: #0284c7; font-weight: 700;">{{ $companyPhone }}</a>.
                                @endif
                            </p>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="background-color: #f8fafc; padding: 30px; text-align: center; border-top: 1px solid #f1f5f9;">
                            <p style="margin: 0; font-size: 12px; color: #94a3b8;">
                                &copy; {{ date('Y') }} {{ $companyName }} &bull; Este é um e-mail automático, não é necessário respondê-lo.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
