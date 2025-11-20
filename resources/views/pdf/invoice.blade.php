<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Orçamento {{ $state['code'] ?? '' }}</title>
    <style>
        @page {
            margin: 0cm;
        }
        body {
            margin: 1.5cm; /* Margem aumentada para respiro */
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 10px; /* Fonte base aumentada */
            line-height: 1.5;
            color: #1f2937; /* Cinza chumbo suave em vez de preto total */
            background-color: #ffffff;
        }
        .container {
            width: 100%;
        }
        
        /* --- HEADER MODERNIZADO --- */
        .header {
            width: 100%;
            margin-bottom: 30px;
            border-bottom: 2px solid #000000; /* Linha forte apenas na base */
            padding-bottom: 15px;
        }
        .header-table {
            width: 100%;
            border-collapse: collapse;
        }
        .header-logo-cell {
            width: 25%;
            vertical-align: middle;
        }
        .header-company-cell {
            width: 45%;
            vertical-align: middle;
            padding-left: 15px;
        }
        .header-budget-cell {
            width: 30%;
            vertical-align: middle;
            text-align: right;
        }

        .logo {
            max-width: 140px;
            max-height: 70px;
            /* Removi o grayscale para ficar mais moderno, se quiser voltar descomente: */
            /* filter: grayscale(100%); */ 
        }

        .company-name {
            font-size: 12px;
            font-weight: 700;
            color: #000000;
            margin-bottom: 4px;
            text-transform: uppercase;
        }
        .company-info {
            font-size: 9px;
            color: #4b5563;
            line-height: 1.4;
        }

        .budget-box {
            background-color: #f3f4f6;
            padding: 10px;
            border-radius: 4px;
            text-align: center;
            border: 1px solid #e5e7eb;
        }
        .budget-title {
            font-size: 16px;
            font-weight: 800;
            color: #000000;
            letter-spacing: 1px;
            margin-bottom: 2px;
        }
        .budget-number {
            font-size: 12px;
            font-weight: 600;
            color: #374151;
            margin-bottom: 5px;
        }
        .budget-date {
            font-size: 8px;
            color: #6b7280;
        }

        /* --- STATUS BADGES COLORIDOS --- */
        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 8px;
            font-weight: 700;
            text-transform: uppercase;
            margin-top: 6px;
        }
        .status-pending { background-color: #fef3c7; color: #92400e; border: 1px solid #f59e0b; } /* Amarelo */
        .status-approved { background-color: #dcfce7; color: #166534; border: 1px solid #22c55e; } /* Verde */
        .status-rejected { background-color: #fee2e2; color: #991b1b; border: 1px solid #ef4444; } /* Vermelho */

        /* --- INFO CLIENTE --- */
        .info-wrapper {
            width: 100%;
            margin-bottom: 25px;
        }
        .info-table {
            width: 100%;
            border-collapse: separate; 
            border-spacing: 10px 0; /* Espaço entre as células */
            margin: 0 -10px; /* Compensar margem */
        }
        .info-box {
            background-color: #f9fafb;
            border: 1px solid #e5e7eb;
            padding: 12px;
            vertical-align: top;
            width: 50%;
            border-radius: 4px;
        }
        .info-title {
            font-size: 9px;
            font-weight: 700;
            color: #111827;
            text-transform: uppercase;
            border-bottom: 1px solid #d1d5db;
            padding-bottom: 5px;
            margin-bottom: 8px;
        }
        .info-line {
            font-size: 9px;
            margin-bottom: 3px;
            color: #374151;
        }
        .info-line strong { color: #000000; }

        /* --- TABELA DE PRODUTOS --- */
        .section-title {
            font-size: 11px;
            font-weight: 700;
            color: #000000;
            text-transform: uppercase;
            margin: 0 0 8px 0;
            padding-left: 5px;
            border-left: 4px solid #000000;
        }
        
        .products-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .products-table th {
            background-color: #111827; /* Preto quase total */
            color: #ffffff;
            font-weight: 600;
            text-align: left;
            padding: 8px 6px;
            font-size: 9px;
            text-transform: uppercase;
        }
        .products-table th:last-child { text-align: right; }
        
        .products-table td {
            padding: 8px 6px;
            border-bottom: 1px solid #e5e7eb;
            font-size: 9px;
            color: #374151;
        }
        .products-table td:last-child { text-align: right; font-weight: 600; }
        /* Zebra Striping */
        .products-table tr:nth-child(even) td { background-color: #f9fafb; }

        /* --- SUMMARY & TOTALS --- */
        .summary-table {
            width: 100%;
            margin-top: 10px;
            border-collapse: collapse;
        }
        .summary-notes-cell {
            width: 60%;
            vertical-align: top;
            padding-right: 20px;
        }
        .summary-totals-cell {
            width: 40%;
            vertical-align: top;
        }

        .notes-content {
            font-size: 8px;
            color: #6b7280;
            line-height: 1.5;
            background: #fff;
            border: 1px dashed #d1d5db;
            padding: 10px;
            border-radius: 4px;
        }
        .notes-list { padding-left: 15px; margin: 5px 0; }

        .totals-box {
            background-color: #ffffff;
            border: 2px solid #000000;
            border-radius: 4px;
            overflow: hidden; /* Para o background do total final não vazar */
        }
        .total-row {
            display: table;
            width: 100%;
            padding: 6px 10px;
            border-bottom: 1px solid #f3f4f6;
        }
        .total-label { display: table-cell; font-size: 9px; font-weight: 600; color: #4b5563; }
        .total-value { display: table-cell; text-align: right; font-size: 9px; font-weight: 600; color: #111827; }
        
        .total-final {
            background-color: #000000;
            color: #ffffff;
            padding: 10px;
            border-bottom: none;
        }
        .total-final .total-label { color: #ffffff; font-size: 10px; vertical-align: middle; }
        .total-final .total-value { color: #ffffff; font-size: 14px; font-weight: 800; vertical-align: middle; }

        /* --- SIGNATURES --- */
        .signatures-table {
            width: 100%;
            margin-top: 60px;
        }
        .signature-col {
            width: 45%;
            text-align: center;
        }
        .signature-space { width: 10%; }
        
        .signature-line {
            border-top: 1px solid #000000;
            padding-top: 8px;
            font-size: 9px;
            font-weight: 700;
            color: #000000;
        }
        .signature-sub { font-size: 8px; color: #6b7280; margin-top: 2px; }

        /* --- FOOTER --- */
        .footer {
            text-align: center;
            margin-top: 40px;
            padding-top: 10px;
            border-top: 1px solid #e5e7eb;
            font-size: 7px;
            color: #9ca3af;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <table class="header-table">
                <tr>
                    <td class="header-logo-cell">
                        @if(isset($layout) && $layout->logo)
                            <img src="{{ Storage::url($layout->logo) }}" alt="Logo" class="logo">
                        @endif
                    </td>
                    <td class="header-company-cell">
                        <div class="company-name">{{ $company->trade_name }}</div>
                        <div class="company-info">
                            {{ $company->address->street ?? $company->address['street'] ?? '' }}, {{ $company->address->number ?? $company->address['number'] ?? '' }}<br>
                            {{ $company->address->city ?? $company->address['city'] ?? '' }}/{{ $company->address->state ?? $company->address['state'] ?? '' }} - {{ $company->address->postcode ?? $company->address['postcode'] ?? '' }}<br>
                            Tel: {{ $company->phone }} | {{ $company->email }}<br>
                            @if($company->document) CNPJ: {{ $company->document }} @endif
                        </div>
                    </td>
                    <td class="header-budget-cell">
                        <div class="budget-box">
                            <div class="budget-title">ORÇAMENTO</div>
                            <div class="budget-number"># {{ $state['code'] ?? '0000' }}</div>
                            <div class="budget-date">{{ date('d/m/Y', strtotime($state['created_at'] ?? now())) }}</div>
                            
                            @php
                                $statusClass = 'status-pending';
                                if(isset($state['status'])) {
                                    if($state['status'] == 'aprovado') $statusClass = 'status-approved';
                                    elseif($state['status'] == 'rejeitado') $statusClass = 'status-rejected';
                                }
                            @endphp
                            <div class="status-badge {{ $statusClass }}">
                                {{ $state['status'] ?? 'Pendente' }}
                            </div>
                        </div>
                    </td>
                </tr>
            </table>
        </div>

        <div class="info-wrapper">
            <table class="info-table">
                <tr>
                    <td class="info-box">
                        <div class="info-title">Dados do Cliente</div>
                        <div class="info-line"><strong>Nome:</strong> {{ $state['content'][0]['customer_name'] ?? 'Consumidor Final' }}</div>
                        <div class="info-line"><strong>E-mail:</strong> {{ $state['content'][0]['customer_email'] ?? '-' }}</div>
                        <div class="info-line"><strong>Tel:</strong> {{ $state['content'][0]['customer_phone'] ?? '-' }}</div>
                        @if(isset($state['content'][0]['street']))
                        <div class="info-line">
                            <strong>End:</strong> {{ $state['content'][0]['street'] }}, {{ $state['content'][0]['number'] }} - {{ $state['content'][0]['city'] }}/{{ $state['content'][0]['state'] }}
                        </div>
                        @endif
                    </td>
                    <td class="info-box">
                        <div class="info-title">Observações</div>
                        <div class="info-line">
                            @if(isset($state['content'][0]['observation']) && !empty($state['content'][0]['observation']))
                                {{ $state['content'][0]['observation'] }}
                            @else
                                <span style="color: #9ca3af; font-style: italic;">Nenhuma observação adicional registrada para este orçamento.</span>
                            @endif
                        </div>
                    </td>
                </tr>
            </table>
        </div>

        <div class="section-title">Itens do Orçamento</div>
        <table class="products-table">
            <thead>
                <tr>
                    <th style="width: 5%;">#</th>
                    <th style="width: 35%;">Produto/Serviço</th>
                    <th style="width: 15%;">Opção</th>
                    <th style="width: 15%;">Local</th>
                    <th style="width: 10%;">Qtd</th>
                    <th style="width: 10%;">Un.</th>
                    <th style="width: 10%;">Total</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $products = $state['content'][0]['products'] ?? [];
                    $productsList = [];
                    $totalItems = 0;

                    // Lógica original de extração de produtos mantida
                    if (isset($products[0]) && is_array($products[0]) && !isset($products[0]['product'])) {
                        foreach ($products as $productGroup) {
                            if (is_array($productGroup) && isset($productGroup[0])) {
                                foreach ($productGroup as $p) {
                                    if (is_array($p) && isset($p['product'])) {
                                        $productsList[] = $p;
                                        $totalItems++;
                                    }
                                }
                            }
                        }
                    } else {
                        $productsList = $products;
                        $totalItems = count($productsList);
                    }
                @endphp

                @foreach($productsList as $index => $product)
                    @php
                        $productObj = \App\Models\Product::find($product['product'] ?? 0);
                        $productName = $productObj ? $productObj->name : ($product_name->name ?? 'Produto Indefinido');
                        $productOption = \App\Models\ProductOption::find($product['product_option'] ?? 0);
                        $location = \App\Models\Location::find($product['location'] ?? 0);
                    @endphp
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>
                            <strong>{{ $productName }}</strong>
                        </td>
                        <td>{{ $productOption ? $productOption->name : '-' }}</td>
                        <td>{{ $location ? $location->name : '-' }}</td>
                        <td>{{ $product['quantity'] ?? 0 }}</td>
                        <td>{{ env('CURRENCY_SUFFIX','R$') }} {{ number_format(($product['price'] ?? 0),2,',','.') }}</td>
                        <td>{{ env('CURRENCY_SUFFIX','R$') }} {{ number_format(($product['subtotal'] ?? 0),2,',','.') }}</td>
                    </tr>
                @endforeach

                @if($totalItems == 0)
                <tr>
                    <td colspan="7" style="text-align: center; padding: 20px; color: #9ca3af;">Nenhum item adicionado.</td>
                </tr>
                @endif
            </tbody>
        </table>

        <table class="summary-table">
            <tr>
                <td class="summary-notes-cell">
                    <div class="notes-content">
                        <strong>Termos e Condições:</strong>
                        <ul class="notes-list">
                            <li>Validade da proposta: <strong>15 dias</strong>.</li>
                            <li>Documento sem valor fiscal.</li>
                            @if(isset($company->budget_information) && $company->budget_information)
                                <li>{{ $company->budget_information }}</li>
                            @endif
                        </ul>
                    </div>
                </td>
                
                <td class="summary-totals-cell">
                    <div class="totals-box">
                        <div class="total-row">
                            <div class="total-label">Volume Total</div>
                            <div class="total-value">{{ $state['content'][0]['quantity'] ?? 0 }} m³</div>
                        </div>
                        <div class="total-row">
                            <div class="total-label">Subtotal</div>
                            <div class="total-value">{{ env('CURRENCY_SUFFIX','R$') }} {{ number_format(($state['content'][0]['total'] ?? 0) + ($state['content'][0]['discount'] ?? 0) - ($state['content'][0]['tax'] ?? 0), 2, ',', '.') }}</div>
                        </div>
                        @if(isset($state['content'][0]['tax']) && $state['content'][0]['tax'] > 0)
                        <div class="total-row">
                            <div class="total-label">Taxas/Adicionais</div>
                            <div class="total-value" style="color: #dc2626;">+ {{ env('CURRENCY_SUFFIX','R$') }} {{ number_format(($state['content'][0]['tax'] ?? 0),2,',','.') }}</div>
                        </div>
                        @endif
                        @if(isset($state['content'][0]['discount']) && $state['content'][0]['discount'] > 0)
                        <div class="total-row">
                            <div class="total-label">Descontos</div>
                            <div class="total-value" style="color: #166534;">- {{ env('CURRENCY_SUFFIX','R$') }} {{ number_format(($state['content'][0]['discount'] ?? 0),2,',','.') }}</div>
                        </div>
                        @endif
                        <div class="total-row total-final">
                            <div class="total-label">TOTAL A PAGAR</div>
                            <div class="total-value">{{ env('CURRENCY_SUFFIX','R$') }} {{ number_format(($state['content'][0]['total'] ?? 0),2,',','.') }}</div>
                        </div>
                    </div>
                </td>
            </tr>
        </table>

        <table class="signatures-table">
            <tr>
                <td class="signature-col">
                    <div class="signature-line">{{ $company->trade_name }}</div>
                    <div class="signature-sub">Responsável Técnico / Vendas</div>
                </td>
                <td class="signature-space"></td>
                <td class="signature-col">
                    <div class="signature-line">Aceite do Cliente</div>
                    <div class="signature-sub">{{ $state['content'][0]['customer_name'] ?? 'Cliente' }}</div>
                </td>
            </tr>
        </table>

        <div class="footer">
            Este documento foi gerado eletronicamente em {{ date('d/m/Y \à\s H:i') }}.<br>
            &copy; {{ date('Y') }} {{ $company->trade_name }}. Todos os direitos reservados.
        </div>
    </div>
</body>
</html>