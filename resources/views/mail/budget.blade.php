<!DOCTYPE html>
<html>

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700&display=swap" rel="stylesheet">
    <title>Orçamento #{{ $budget->code ?? '' }}</title>
    <style>
        body, table, td, a { -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%; }
        table, td { mso-table-lspace: 0pt; mso-table-rspace: 0pt; }
        img { -ms-interpolation-mode: bicubic; border: 0; height: auto; line-height: 100%; outline: none; text-decoration: none; }
        table { border-collapse: collapse !important; }
        body { height: 100% !important; margin: 0 !important; padding: 0 !important; width: 100% !important; }

        @media screen and (max-width: 600px) {
            .container { width: 100% !important; }
            .content-padding { padding: 20px !important; }
        }
    </style>
</head>

<body style="background-color: #f4f4f5; margin: 0; padding: 0; font-family: 'Inter', Helvetica, Arial, sans-serif;">
    
    <table border="0" cellpadding="0" cellspacing="0" width="100%">
        <tr>
            <td style="padding: 40px 20px;" align="center">
                
                <table border="0" cellpadding="0" cellspacing="0" width="600" class="container" style="background-color: #ffffff; border-radius: 12px; overflow: hidden; border: 1px solid #e4e4e7;">
                    
                    <!-- Header -->
                    <tr>
                        <td style="background-color: #000000; padding: 40px; text-align: center;">
                            @if(isset($layout->logo) && $layout->logo)
                                <img src="{{ $message->embed(storage_path('app/public/' . $layout->logo)) }}" alt="Logo" style="max-height: 60px; width: auto;">
                            @else
                                <h1 style="color: #ffffff; font-size: 24px; font-weight: 800; margin: 0; text-transform: uppercase; letter-spacing: 2px;">{{ $company->trade_name ?? config('app.name') }}</h1>
                            @endif
                        </td>
                    </tr>

                    <!-- Body -->
                    <tr>
                        <td class="content-padding" style="padding: 50px 50px 40px;">
                            
                            <h2 style="margin: 0 0 20px; color: #18181b; font-size: 22px; font-weight: 700; line-height: 1.2;">Olá, {{ data_get($budget->content, 'customer_name', 'Cliente') }}!</h2>
                            
                            <p style="margin: 0 0 30px; color: #52525b; font-size: 16px; line-height: 1.6;">
                                Conforme conversamos, preparamos o orçamento detalhado para o seu projeto. Abaixo você encontra um resumo da proposta:
                            </p>

                            <!-- Items Table -->
                            <table border="0" cellpadding="0" cellspacing="0" width="100%" style="margin-bottom: 30px; border-collapse: collapse;">
                                <thead>
                                    <tr>
                                        <th align="left" style="padding: 12px 10px; border-bottom: 2px solid #f4f4f5; font-size: 12px; color: #71717a; text-transform: uppercase;">Item</th>
                                        <th align="center" style="padding: 12px 10px; border-bottom: 2px solid #f4f4f5; font-size: 12px; color: #71717a; text-transform: uppercase;">Qtd</th>
                                        <th align="right" style="padding: 12px 10px; border-bottom: 2px solid #f4f4f5; font-size: 12px; color: #71717a; text-transform: uppercase;">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach(data_get($budget->content, 'products', []) as $product)
                                        @php
                                            $productObj = \App\Models\Product::find($product['product'] ?? 0);
                                        @endphp
                                        <tr>
                                            <td style="padding: 12px 10px; border-bottom: 1px solid #f4f4f5; font-size: 14px; color: #18181b;">
                                                {{ $productObj ? $productObj->name : 'Produto' }}
                                            </td>
                                            <td align="center" style="padding: 12px 10px; border-bottom: 1px solid #f4f4f5; font-size: 14px; color: #18181b;">
                                                {{ $product['quantity'] ?? 0 }}
                                            </td>
                                            <td align="right" style="padding: 12px 10px; border-bottom: 1px solid #f4f4f5; font-size: 14px; color: #18181b; font-weight: 600;">
                                                {{ env('CURRENCY_SYMBOL', 'R$') }} {{ number_format($product['subtotal'] ?? 0, 2, ',', '.') }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>

                            <!-- Financial Summary Card -->
                            <table border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #fafafa; border: 1px solid #f4f4f5; border-radius: 8px; margin-bottom: 30px;">
                                <tr>
                                    <td style="padding: 30px;">
                                        <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                            @if(floatval(data_get($budget->content, 'shipping', 0)) > 0)
                                            <tr>
                                                <td style="padding-bottom: 10px; font-size: 14px; color: #71717a;">Frete/Entrega</td>
                                                <td align="right" style="padding-bottom: 10px; font-size: 14px; color: #18181b;">+ {{ env('CURRENCY_SYMBOL', 'R$') }} {{ number_format(data_get($budget->content, 'shipping', 0), 2, ',', '.') }}</td>
                                            </tr>
                                            @endif
                                            
                                            @if(floatval(data_get($budget->content, 'tax', 0)) > 0)
                                            <tr>
                                                <td style="padding-bottom: 10px; font-size: 14px; color: #71717a;">Taxas/Adicionais</td>
                                                <td align="right" style="padding-bottom: 10px; font-size: 14px; color: #18181b; color: #dc2626;">+ {{ env('CURRENCY_SYMBOL', 'R$') }} {{ number_format(data_get($budget->content, 'tax', 0), 2, ',', '.') }}</td>
                                            </tr>
                                            @endif

                                            @if(floatval(data_get($budget->content, 'discount', 0)) > 0)
                                            <tr>
                                                <td style="padding-bottom: 10px; font-size: 14px; color: #71717a;">Descontos</td>
                                                <td align="right" style="padding-bottom: 10px; font-size: 14px; color: #166534;">- {{ env('CURRENCY_SYMBOL', 'R$') }} {{ number_format(data_get($budget->content, 'discount', 0), 2, ',', '.') }}</td>
                                            </tr>
                                            @endif

                                            <tr>
                                                <td style="padding-top: 15px; border-top: 1px solid #e4e4e7;">
                                                    <span style="display: block; font-size: 12px; color: #71717a; text-transform: uppercase; font-weight: 700; margin-bottom: 4px;">Investimento Total</span>
                                                    <span style="font-size: 32px; color: #000000; font-weight: 800; letter-spacing: -1px;">
                                                        {{ env('CURRENCY_SYMBOL', 'R$') }} {{ number_format(data_get($budget->content, 'total', 0), 2, ',', '.') }}
                                                    </span>
                                                </td>
                                                <td align="right" style="padding-top: 15px; border-top: 1px solid #e4e4e7; vertical-align: bottom;">
                                                    <span style="display: block; font-size: 12px; color: #71717a; text-transform: uppercase; font-weight: 700; margin-bottom: 4px;">Ref. Orçamento</span>
                                                    <span style="font-size: 16px; color: #18181b; font-weight: 600;">#{{ $budget->code }}</span>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            <p style="margin: 0 0 40px; color: #52525b; font-size: 15px; line-height: 1.6;">
                                O arquivo em anexo contém todas as especificações técnicas e validade desta proposta. Caso tenha qualquer dúvida, basta responder a este e-mail.
                            </p>

                            <!-- Contact Info -->
                            <table border="0" cellpadding="0" cellspacing="0" width="100%" style="border-top: 1px solid #e4e4e7; padding-top: 30px;">
                                <tr>
                                    <td>
                                        <p style="margin: 0; font-size: 14px; color: #18181b; font-weight: 700;">Atenciosamente,</p>
                                        <p style="margin: 4px 0 0; font-size: 14px; color: #71717a;">Equipe {{ $company->trade_name ?? config('app.name') }}</p>
                                    </td>
                                    <td style="text-align: right;">
                                        <p style="margin: 0; font-size: 13px; color: #71717a;">
                                            📞 {{ $company->phone }}<br>
                                            ✉️ {{ $company->email }}
                                        </p>
                                    </td>
                                </tr>
                            </table>

                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="background-color: #fafafa; padding: 30px; text-align: center; border-top: 1px solid #e4e4e7;">
                            <p style="margin: 0; font-size: 12px; color: #a1a1aa; line-height: 1.5;">
                                <strong>{{ $company->legal_name ?? $company->trade_name }}</strong><br>
                                {{ $company->address->street ?? '' }}, {{ $company->address->number ?? '' }} - {{ $company->address->city ?? '' }}/{{ $company->address->state ?? '' }}
                            </p>
                        </td>
                    </tr>

                </table>
                
                <p style="margin: 30px 0 0; font-size: 11px; color: #a1a1aa; text-align: center;">
                    Este é um e-mail gerado automaticamente pelo sistema de orçamentos da {{ config('app.name') }}.
                </p>

            </td>
        </tr>
    </table>

</body>
</html>