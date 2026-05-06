<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&display=swap" rel="stylesheet">
    <title>Novo Orçamento Recebido</title>
    <style>
        body, table, td, a { -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%; }
        table, td { mso-table-lspace: 0pt; mso-table-rspace: 0pt; }
        img { -ms-interpolation-mode: bicubic; border: 0; height: auto; line-height: 100%; outline: none; text-decoration: none; }
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
                            <span style="display: inline-block; background-color: #38bdf8; color: #0f172a; font-size: 12px; font-weight: 800; text-transform: uppercase; padding: 4px 12px; border-radius: 9999px; margin-bottom: 16px; letter-spacing: 1px;">Novo Pedido</span>
                            <h1 style="color: #ffffff; font-size: 28px; font-weight: 800; margin: 0; letter-spacing: -1px;">Novo Orçamento Recebido!</h1>
                        </td>
                    </tr>

                    <!-- Body -->
                    <tr>
                        <td class="content" style="padding: 40px 50px;">
                            <p style="font-size: 16px; line-height: 1.6; color: #64748b; margin: 0 0 30px;">
                                Olá, administrador. Um novo pedido de orçamento acaba de ser realizado através do site da <strong>{{ $company->trade_name ?? config('app.name') }}</strong>.
                            </p>

                            <!-- Summary Card -->
                            <table border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #f1f5f9; border-radius: 12px; margin-bottom: 30px;">
                                <tr>
                                    <td style="padding: 24px;">
                                        <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                            <tr>
                                                <td style="padding-bottom: 20px; border-bottom: 1px solid #e2e8f0;">
                                                    <span style="display: block; font-size: 11px; font-weight: 700; color: #94a3b8; text-transform: uppercase; margin-bottom: 4px;">Código do Orçamento</span>
                                                    <span style="font-size: 20px; font-weight: 700; color: #0f172a;">#{{ $budget->code }}</span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 20px 0; border-bottom: 1px solid #e2e8f0;">
                                                    <span style="display: block; font-size: 11px; font-weight: 700; color: #94a3b8; text-transform: uppercase; margin-bottom: 4px;">Cliente</span>
                                                    <span style="font-size: 16px; font-weight: 600; color: #1e293b;">{{ $customerName }}</span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 20px 0; border-bottom: 1px solid #e2e8f0;">
                                                    <span style="display: block; font-size: 11px; font-weight: 700; color: #94a3b8; text-transform: uppercase; margin-bottom: 4px;">WhatsApp / Contato</span>
                                                    <span style="font-size: 16px; font-weight: 600; color: #1e293b;">{{ $customerPhone }}</span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 20px 0;">
                                                    <span style="display: block; font-size: 11px; font-weight: 700; color: #94a3b8; text-transform: uppercase; margin-bottom: 4px;">Valor Estimado <small style="font-weight: 400; text-transform: none; color: #cbd5e1;">(Sem frete e outras despesas)</small></span>
                                                    <span style="font-size: 24px; font-weight: 800; color: #0284c7;">R$ {{ number_format($totalValue, 2, ',', '.') }}</span>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            <!-- CTA -->
                            <div style="text-align: center;">
                                <a href="{{ $adminUrl }}" style="display: inline-block; background-color: #0284c7; color: #ffffff; font-size: 16px; font-weight: 700; text-decoration: none; padding: 16px 32px; border-radius: 8px; box-shadow: 0 4px 6px -1px rgba(2, 132, 199, 0.3);">
                                    Acessar Painel de Controle
                                </a>
                            </div>

                            <p style="font-size: 14px; line-height: 1.6; color: #94a3b8; margin: 30px 0 0; text-align: center;">
                                Recomendamos que você processe este pedido e envie a resposta ao cliente o mais breve possível para garantir a melhor taxa de conversão.
                            </p>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="background-color: #f8fafc; padding: 30px; text-align: center; border-top: 1px solid #f1f5f9;">
                            <p style="margin: 0; font-size: 12px; color: #94a3b8;">
                                Sistema de Notificações Internas &bull; {{ config('app.name') }}
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
