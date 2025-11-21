<!DOCTYPE html>
<html>

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700&display=swap" rel="stylesheet">
    <title>Orçamento #{{ $budget['code'] ?? '' }}</title>
    <style>
        /* Resets básicos para garantir compatibilidade entre clientes de e-mail */
        body, table, td, a { -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%; }
        table, td { mso-table-lspace: 0pt; mso-table-rspace: 0pt; }
        img { -ms-interpolation-mode: bicubic; }
        
        /* Estilos Responsivos */
        @media screen and (max-width: 600px) {
            .container { width: 100% !important; }
            .content-padding { padding: 20px !important; }
        }
    </style>
</head>

<body style="background-color: #f3f4f6; margin: 0; padding: 0; font-family: 'Inter', Helvetica, Arial, sans-serif; -webkit-font-smoothing: antialiased;">
    
    <table border="0" cellpadding="0" cellspacing="0" width="100%">
        <tr>
            <td style="padding: 40px 20px;">
                
                <table align="center" border="0" cellpadding="0" cellspacing="0" width="600" class="container" style="background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);">
                    
                    <tr>
                        <td style="background-color: #18181b; padding: 30px 40px; text-align: center;">
                            @if(isset($layout) && $layout->logo)
                                <img src="{{ Storage::url($layout->logo) }}" alt="Logo" style="max-height: 50px; max-width: 150px; display: block; margin: 0 auto;">
                            @else
                                <h1 style="color: #ffffff; font-size: 24px; font-weight: 700; margin: 0; letter-spacing: -0.5px;">{{ $company->trade_name }}</h1>
                            @endif
                        </td>
                    </tr>

                    <tr>
                        <td class="content-padding" style="padding: 40px 40px 30px;">
                            
                            <p style="margin: 0 0 20px; color: #374151; font-size: 16px; line-height: 1.6;">
                                Olá, <strong>{{ $budget['content']['customer_name'] ?? 'Cliente' }}</strong>.
                            </p>
                            <p style="margin: 0 0 30px; color: #6b7280; font-size: 15px; line-height: 1.6;">
                                Conforme solicitado, segue em anexo o detalhamento do seu orçamento. Abaixo estão os dados principais para sua conferência:
                            </p>

                            <table border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #f9fafb; border: 1px solid #e5e7eb; border-radius: 8px;">
                                <tr>
                                    <td style="padding: 25px;">
                                        <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                            <tr>
                                                <td valign="top" style="padding-bottom: 15px;">
                                                    <p style="margin: 0 0 5px; font-size: 12px; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px; font-weight: 600;">Código</p>
                                                    <p style="margin: 0; font-size: 16px; color: #111827; font-weight: 500;">#{{ $budget['code'] ?? 'N/A' }}</p>
                                                </td>
                                                <td valign="top" style="padding-bottom: 15px;">
                                                    <p style="margin: 0 0 5px; font-size: 12px; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px; font-weight: 600;">Data</p>
                                                    <p style="margin: 0; font-size: 16px; color: #111827; font-weight: 500;">
                                                        {{ isset($budget['created_at']) ? date('d/m/Y', strtotime($budget['created_at'])) : date('d/m/Y') }}
                                                    </p>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td colspan="2" style="padding-top: 15px; border-top: 1px dashed #d1d5db;">
                                                    <p style="margin: 0 0 5px; font-size: 12px; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px; font-weight: 600;">Valor Total</p>
                                                    <p style="margin: 0; font-size: 24px; color: #111827; font-weight: 700; letter-spacing: -0.5px;">
                                                        {{ env('CURRENCY_SUFFIX', 'R$') }} {{ $budget['content']['products'][0]['subtotal'] ?? '0,00' }}
                                                    </p>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            <p style="margin: 30px 0 10px; color: #374151; font-size: 15px; line-height: 1.6;">
                                O arquivo PDF com todos os detalhes técnicos e condições de pagamento encontra-se em anexo.
                            </p>
                            <p style="margin: 0 0 30px; color: #374151; font-size: 15px; line-height: 1.6;">
                                Ficamos à disposição para quaisquer dúvidas.
                            </p>

                            <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                <tr>
                                    <td style="padding: 15px; background-color: #f3f4f6; border-radius: 6px; text-align: center;">
                                        <p style="margin: 0; font-size: 14px; color: #4b5563;">
                                            <strong style="color: #111827;">Dúvidas? Fale conosco:</strong><br>
                                            <span style="display: inline-block; margin-top: 5px;">
                                                📞 {{ $company->phone }} &nbsp;|&nbsp; ✉️ {{ $company->email }}
                                            </span>
                                        </p>
                                    </td>
                                </tr>
                            </table>

                        </td>
                    </tr>

                    <tr>
                        <td style="background-color: #f9fafb; padding: 20px; text-align: center; border-top: 1px solid #e5e7eb;">
                            <p style="margin: 0 0 5px; font-size: 12px; color: #9ca3af;">
                                {{ $company->trade_name }} &copy; {{ date('Y') }}
                            </p>
                            <p style="margin: 0; font-size: 11px; color: #d1d5db;">
                                Este é um e-mail automático, por favor não responda.
                            </p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>

</body>
</html>