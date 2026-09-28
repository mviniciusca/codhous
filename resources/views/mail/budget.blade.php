<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <title>Seu Orçamento Chegou!</title>
    <style>
        body, table, td, a { -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%; }
        table, td { mso-table-lspace: 0pt; mso-table-rspace: 0pt; }
        img { -ms-interpolation-mode: bicubic; border: 0; outline: none; text-decoration: none; }
        table { border-collapse: collapse !important; }
        body { height: 100% !important; margin: 0 !important; padding: 0 !important; width: 100% !important; background-color: #ffffff; font-family: 'Inter', Helvetica, Arial, sans-serif; color: #111111; }
        
        .header { background-color: #111111; padding: 25px 40px; }
        .header-table { width: 100%; }
        .header-logo { color: #ffffff; font-size: 14px; font-weight: 700; letter-spacing: 2px; text-transform: uppercase; }
        .header-label { color: #888888; font-size: 11px; font-weight: 600; letter-spacing: 1px; text-transform: uppercase; text-align: right; }
        
        .main-container { max-width: 650px; margin: 0 auto; width: 100%; }
        .content { padding: 50px 40px; }
        
        .sub-title { font-size: 14px; color: #777777; margin-bottom: 8px; }
        .main-title { font-size: 34px; font-weight: 300; color: #111111; margin: 0 0 30px 0; letter-spacing: -1px; }
        
        .greeting { font-size: 16px; color: #555555; line-height: 1.6; margin-bottom: 40px; }
        
        .table-wrapper { border-left: 2px solid #eaeaea; padding-left: 20px; margin-bottom: 40px; }
        .status-row { font-size: 14px; margin-bottom: 25px; color: #555555; }
        
        .items-table { width: 100%; font-size: 14px; }
        .items-table th { padding: 12px 8px; text-align: left; font-weight: 700; border-bottom: 1px solid #eaeaea; color: #111111; }
        .items-table td { padding: 16px 8px; border-bottom: 1px solid #f9f9f9; color: #555555; vertical-align: top; }
        
        .totals-table { width: 100%; margin-top: 15px; font-size: 14px; color: #555555; }
        .totals-table td { padding: 6px 8px; text-align: right; }
        .total-row td { font-weight: 700; color: #111111; font-size: 15px; padding-top: 15px; }
        
        .cta-button { display: block; width: 100%; background-color: #111111; color: #ffffff; text-align: center; padding: 18px 0; font-size: 14px; font-weight: 600; text-decoration: none; margin: 40px 0; }
        
        .help-text { font-size: 14px; color: #777777; margin-bottom: 40px; }
        .help-link { color: #111111; font-weight: 600; text-decoration: underline; }
        
        .footer { border-top: 1px solid #eaeaea; padding: 40px 40px 60px 40px; }
        .footer-brand { font-size: 12px; font-weight: 700; letter-spacing: 2px; color: #111111; text-transform: uppercase; margin-bottom: 12px; }
        .footer-text { font-size: 11px; color: #999999; line-height: 1.5; margin: 0; }
        
        @media screen and (max-width: 600px) {
            .content, .header, .footer { padding-left: 20px !important; padding-right: 20px !important; }
            .header-label { display: none !important; }
            .main-title { font-size: 28px !important; }
        }
    </style>
</head>
<body>
    <table border="0" cellpadding="0" cellspacing="0" width="100%">
        <tr>
            <td align="center">
                <table class="main-container" border="0" cellpadding="0" cellspacing="0">
                    <!-- Header -->
                    <tr>
                        <td class="header">
                            <table class="header-table" border="0" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td class="header-logo">
                                        {{ mb_strtoupper(config('app.name')) }}
                                    </td>
                                    <td class="header-label">
                                        PROPOSTA COMERCIAL
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    
                    <!-- Content -->
                    <tr>
                        <td class="content">
                            <div class="sub-title">Orçamento #{{ $budget->code }}</div>
                            <h1 class="main-title">Seu orçamento está pronto.</h1>
                            
                            <div class="greeting">
                                Olá, {{ data_get($budget->content, 'customer_name', 'Cliente') }}. Preparamos o orçamento detalhado para o seu projeto. Você pode conferir os principais itens abaixo e todos os detalhes no arquivo PDF em anexo.
                            </div>
                            
                            <div class="table-wrapper">
                                <div class="status-row">
                                    <strong>Status:</strong> Aguardando Aprovação
                                </div>
                                
                                <table class="items-table" border="0" cellpadding="0" cellspacing="0">
                                    <tr>
                                        <th width="65%">Produto</th>
                                        <th width="10%">Qtd</th>
                                        <th width="25%" style="text-align: right;">Preço</th>
                                    </tr>
                                    @php
                                        $subtotal = 0;
                                    @endphp
                                    @foreach(data_get($budget->content, 'products', []) as $product)
                                        @php 
                                            $productObj = \App\Models\Product::find($product['product'] ?? 0); 
                                            $itemSub = floatval($product['subtotal'] ?? 0);
                                            $subtotal += $itemSub;
                                        @endphp
                                        <tr>
                                            <td>{{ $productObj ? $productObj->name : 'Produto' }}</td>
                                            <td>{{ $product['quantity'] ?? 0 }}</td>
                                            <td style="text-align: right;">{{ env('CURRENCY_SYMBOL', 'R$') }} {{ number_format($itemSub, 2, ',', '.') }}</td>
                                        </tr>
                                    @endforeach
                                </table>
                                
                                <table class="totals-table" border="0" cellpadding="0" cellspacing="0">
                                    <tr>
                                        <td width="75%">Subtotal:</td>
                                        <td width="25%">{{ env('CURRENCY_SYMBOL', 'R$') }} {{ number_format($subtotal, 2, ',', '.') }}</td>
                                    </tr>
                                    @if(floatval(data_get($budget->content, 'shipping', 0)) > 0)
                                    <tr>
                                        <td>Frete:</td>
                                        <td>{{ env('CURRENCY_SYMBOL', 'R$') }} {{ number_format(data_get($budget->content, 'shipping', 0), 2, ',', '.') }}</td>
                                    </tr>
                                    @endif
                                    @if(floatval(data_get($budget->content, 'discount', 0)) > 0)
                                    <tr>
                                        <td>Desconto:</td>
                                        <td>- {{ env('CURRENCY_SYMBOL', 'R$') }} {{ number_format(data_get($budget->content, 'discount', 0), 2, ',', '.') }}</td>
                                    </tr>
                                    @endif
                                    <tr class="total-row">
                                        <td>Total:</td>
                                        <td>{{ env('CURRENCY_SYMBOL', 'R$') }} {{ number_format(data_get($budget->content, 'total', 0), 2, ',', '.') }}</td>
                                    </tr>
                                </table>
                            </div>
                            
                            <a href="https://wa.me/{{ preg_replace('/\D/', '', $company->phone ?? '') }}" class="cta-button">
                                Falar com Consultor ↗
                            </a>
                            
                            <div class="help-text">
                                Precisa de ajuda? <a href="https://wa.me/{{ preg_replace('/\D/', '', $company->phone ?? '') }}" class="help-link">Fale com nosso suporte</a>.
                            </div>
                        </td>
                    </tr>
                    
                    <!-- Footer -->
                    <tr>
                        <td class="footer">
                            <div class="footer-brand">{{ mb_strtoupper($company->trade_name ?? config('app.name')) }}</div>
                            <p class="footer-text">
                                Este é um e-mail automático. Não é necessário respondê-lo.<br>
                                &copy; {{ date('Y') }} {{ $company->trade_name ?? config('app.name') }} - Todos os direitos reservados.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>