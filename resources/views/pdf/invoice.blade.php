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
            margin: 1cm;
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 9px;
            line-height: 1.4;
            color: #000000;
            background-color: #ffffff;
        }
        .container {
            width: 100%;
        }
        .header {
            display: table;
            width: 100%;
            border: 2px solid #000000;
            margin-bottom: 15px;
        }
        .header-cell {
            display: table-cell;
            padding: 8px;
            vertical-align: middle;
            border-right: 1px solid #000000;
        }
        .header-cell:last-child {
            border-right: none;
        }
        .logo-section {
            /* width: 25%; removed */
        }
        .logo {
            max-width: 120px;
            max-height: 60px;
            filter: grayscale(100%);
        }
        .company-section {
            width: 65%;
        }
        .company-name {
            font-size: 11px;
            font-weight: bold;
            color: #000000;
            margin-bottom: 3px;
        }
        .company-info {
            font-size: 8px;
            color: #000000;
            line-height: 1.5;
        }
        .budget-section {
            width: 35%;
            background-color: #f3f4f6;
        }
        .budget-title {
            font-size: 18px;
            font-weight: bold;
            color: #000000;
            margin-bottom: 3px;
        }
        .budget-number {
            font-size: 14px;
            font-weight: bold;
            color: #000000;
            margin-bottom: 3px;
        }
        .budget-date {
            font-size: 8px;
            color: #000000;
        }
        .status-badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 8px;
            font-weight: 600;
            text-transform: uppercase;
            margin-top: 3px;
            border: 1px solid #000000;
        }
        .status-pending { background-color: #ffffff; color: #000000; }
        .status-approved { background-color: #e5e7eb; color: #000000; }
        .status-rejected { background-color: #000000; color: #ffffff; }
        
        .info-row {
            display: table;
            width: 100%;
            margin-bottom: 10px;
        }
        .info-cell {
            display: table-cell;
            width: 50%;
            padding: 8px;
            background-color: #ffffff;
            border: 1px solid #000000;
            vertical-align: top;
        }
        .info-cell:first-child {
            border-right: none;
        }
        .info-title {
            font-size: 9px;
            font-weight: 700;
            color: #000000;
            margin-bottom: 5px;
            padding-bottom: 3px;
            border-bottom: 1px solid #000000;
        }
        .info-line {
            font-size: 8px;
            color: #000000;
            line-height: 1.6;
            margin-bottom: 2px;
        }
        .info-line strong {
            color: #000000;
        }
        
        .section-title {
            font-size: 10px;
            font-weight: 700;
            color: #ffffff;
            background-color: #000000;
            padding: 5px 8px;
            margin: 15px 0 8px 0;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
            font-size: 8px;
        }
        th {
            background-color: #e5e7eb;
            color: #000000;
            font-weight: 600;
            text-align: left;
            padding: 6px 4px;
            font-size: 8px;
            text-transform: uppercase;
            border: 1px solid #000000;
        }
        th:last-child { text-align: right; }
        td {
            padding: 5px 4px;
            border: 1px solid #000000;
            font-size: 8px;
            color: #000000;
        }
        td:last-child { text-align: right; }
        tr:nth-child(even) td { background-color: #f9fafb; }
        
        .summary-row {
            display: table;
            width: 100%;
            margin-top: 15px;
        }
        .summary-cell {
            display: table-cell;
            vertical-align: top;
        }
        .summary-left {
            width: 58%;
            padding-right: 10px;
        }
        .summary-right {
            width: 42%;
        }
        .notes-box {
            background-color: #ffffff;
            border: 1px solid #000000;
            padding: 8px;
            font-size: 7px;
            line-height: 1.6;
        }
        .notes-title {
            font-size: 8px;
            font-weight: 700;
            color: #000000;
            margin-bottom: 5px;
            border-bottom: 1px solid #000000;
            padding-bottom: 2px;
        }
        .notes-list {
            color: #000000;
            padding-left: 15px;
        }
        .notes-list li {
            margin-bottom: 3px;
        }
        
        .totals-box {
            background-color: #ffffff;
            border: 2px solid #000000;
            padding: 0;
        }
        .total-line {
            display: table;
            width: 100%;
            padding: 4px 8px;
            font-size: 8px;
        }
        .total-line:first-child {
            padding-top: 8px;
        }
        .total-label {
            display: table-cell;
            color: #000000;
            font-weight: 500;
        }
        .total-value {
            display: table-cell;
            text-align: right;
            color: #000000;
            font-weight: 600;
        }
        .total-final {
            background-color: #000000;
            color: #ffffff;
            margin: 0;
            padding: 8px;
            font-size: 10px;
        }
        .total-final .total-label {
            color: #ffffff;
            font-weight: 700;
        }
        .total-final .total-value {
            color: #ffffff;
            font-weight: 700;
            font-size: 12px;
        }        .signatures {
            display: table;
            width: 100%;
            margin: 30px 0 20px;
        }
        .signature-cell {
            display: table-cell;
            width: 50%;
            text-align: center;
            padding: 0 15px;
        }
        .signature-line {
            border-top: 1px solid #000000;
            padding-top: 5px;
            margin-top: 40px;
            font-size: 9px;
            font-weight: 600;
            color: #000000;
        }
        .signature-detail {
            font-size: 7px;
            color: #000000;
            margin-top: 3px;
        }
        
        .footer {
            text-align: center;
            margin-top: 20px;
            padding-top: 10px;
            border-top: 1px solid #000000;
            font-size: 7px;
            color: #000000;
        }
    </style>
    <style>
        .watermark {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 72px;
            color: rgba(0, 0, 0, 0.05);
            pointer-events: none;
            z-index: -1;
            white-space: nowrap;
        }
    </style>
</head>

<body>
    <div class="container">
        <!-- Header Section -->
        <div class="header">
            <div class="header-cell company-section">
                <table style="width: 100%; border: none; margin: 0;">
                    <tr>
                        <td style="width: 130px; border: none; padding: 0 15px 0 0; vertical-align: middle;">
                            @if(isset($layout) && $layout->logo)
                                <img src="{{ Storage::url($layout->logo) }}" alt="Logo" class="logo">
                            @endif
                        </td>
                        <td style="border: none; padding: 0; vertical-align: middle;">
                            <div class="company-name">{{ $company->trade_name }}</div>
                            <div class="company-info">
                                {{ $company->address->street ?? $company->address['street'] ?? '' }}, {{ $company->address->number ?? $company->address['number'] ?? '' }}<br>
                                {{ $company->address->city ?? $company->address['city'] ?? '' }}/{{ $company->address->state ?? $company->address['state'] ?? '' }} - CEP {{ $company->address->postcode ?? $company->address['postcode'] ?? '' }}<br>
                                Tel: {{ $company->phone }} | E-mail: {{ $company->email }}
                                @if($company->document)
                                    <br>CNPJ: {{ $company->document }}
                                @endif
                            </div>
                        </td>
                    </tr>
                </table>
            </div>
            <div class="header-cell budget-section">
                <div class="budget-title">ORÇAMENTO</div>
                <div class="budget-number">Nº {{ $state['code'] ?? 'N/A' }}</div>
                <div class="budget-date">Emissão: {{ date('d/m/Y H:i', strtotime($state['created_at'] ?? now())) }}</div>
                @php
                    $statusClass = 'status-pending';
                    if(isset($state['status'])) {
                        if($state['status'] == 'aprovado') $statusClass = 'status-approved';
                        elseif($state['status'] == 'rejeitado') $statusClass = 'status-rejected';
                    }
                @endphp
                <div class="status-badge {{ $statusClass }}">
                    {{ strtoupper($state['status'] ?? 'Pendente') }}
                </div>
            </div>
        </div>

        <!-- Company and Customer Info -->
        <div class="info-row">
            <div class="info-cell">
                <div class="info-title">DADOS DO CLIENTE</div>
                <div class="info-line"><strong>Cliente:</strong> {{ $state['content'][0]['customer_name'] ?? 'N/A' }}</div>
                <div class="info-line"><strong>E-mail:</strong> {{ $state['content'][0]['customer_email'] ?? 'N/A' }}</div>
                <div class="info-line"><strong>Telefone:</strong> {{ $state['content'][0]['customer_phone'] ?? 'N/A' }}</div>
                @if(isset($state['content'][0]['street']))
                <div class="info-line"><strong>Endereço:</strong> {{ $state['content'][0]['street'] }}, {{ $state['content'][0]['number'] }} - {{ $state['content'][0]['neighborhood'] }}</div>
                <div class="info-line"><strong>Cidade:</strong> {{ $state['content'][0]['city'] }}/{{ $state['content'][0]['state'] }} - CEP {{ $state['content'][0]['postcode'] }}</div>
                @endif
            </div>
            <div class="info-cell">
                <div class="info-title">OBSERVAÇÕES</div>
                @if(isset($state['content'][0]['observation']))
                <div class="info-line">{{ $state['content'][0]['observation'] }}</div>
                @else
                <div class="info-line" style="color: #9ca3af;">Nenhuma observação adicional</div>
                @endif
            </div>
        </div>

        <!-- Products Table -->
        <div class="section-title">PRODUTOS E SERVIÇOS</div>
        <table>
            <thead>
                <tr>
                    <th style="width: 4%;">#</th>
                    <th style="width: 32%;">Produto</th>
                    <th style="width: 18%;">Opção</th>
                    <th style="width: 18%;">Local</th>
                    <th style="width: 8%;">Qtd</th>
                    <th style="width: 10%;">Preço Un</th>
                    <th style="width: 10%;">Total</th>
                </tr>
            </thead>
            <tbody>
                    @php
                    $products = $state['content'][0]['products'] ?? [];
                    $productsList = [];
                    $totalItems = 0;

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
                    $productName = $productObj ? $productObj->name : ($product_name->name ?? 'Produto');
                    $productOption = \App\Models\ProductOption::find($product['product_option'] ?? 0);
                    $location = \App\Models\Location::find($product['location'] ?? 0);
                    @endphp
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $productName }}</td>
                        <td>{{ $productOption ? $productOption->name : '-' }}</td>
                        <td>{{ $location ? $location->name : '-' }}</td>
                        <td>{{ $product['quantity'] ?? 0 }} m³</td>
                        <td>{{ env('CURRENCY_SUFFIX','R$') }} {{ number_format(($product['price'] ?? 0),2,',','.') }}</td>
                        <td>{{ env('CURRENCY_SUFFIX','R$') }} {{ number_format(($product['subtotal'] ?? 0),2,',','.') }}</td>
                    </tr>
                    @endforeach

                    @if($totalItems == 0)
                    <tr>
                        <td colspan="7" style="text-align: center; color: #9ca3af;">Nenhum produto encontrado</td>
                    </tr>
                    @endif
                </tbody>
        </table>

        <!-- Summary and Totals -->
        <div class="summary-row">
            <div class="summary-cell summary-left">
                <div class="notes-box">
                    <div class="notes-title">Observações Importantes</div>
                    <ul class="notes-list">
                        <li>Orçamento válido por 15 dias a partir da data de emissão.</li>
                        <li>Este documento é apenas um orçamento e não possui valor fiscal.</li>
                        <li>Forma de pagamento e prazo de entrega a combinar.</li>
                        @if(isset($company->budget_information) && $company->budget_information)
                        <li>{{ $company->budget_information }}</li>
                        @endif
                    </ul>
                </div>
            </div>
            <div class="summary-cell summary-right">
                <div class="totals-box">
                    <div class="total-line">
                        <div class="total-label">Quantidade Total:</div>
                        <div class="total-value">{{ $state['content'][0]['quantity'] ?? 0 }} m³</div>
                    </div>
                    <div class="total-line">
                        <div class="total-label">Taxa Adicional:</div>
                        <div class="total-value">{{ env('CURRENCY_SUFFIX','R$') }} {{ number_format(($state['content'][0]['tax'] ?? 0),2,',','.') }}</div>
                    </div>
                    <div class="total-line">
                        <div class="total-label">Desconto:</div>
                        <div class="total-value">-{{ env('CURRENCY_SUFFIX','R$') }} {{ number_format(($state['content'][0]['discount'] ?? 0),2,',','.') }}</div>
                    </div>
                    <div class="total-line total-final">
                        <div class="total-label">VALOR TOTAL</div>
                        <div class="total-value">{{ env('CURRENCY_SUFFIX','R$') }} {{ number_format(($state['content'][0]['total'] ?? 0),2,',','.') }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Signatures -->
        <div class="signatures">
            <div class="signature-cell">
                <div class="signature-line">{{ $company->trade_name }}</div>
                <div class="signature-detail">CNPJ: {{ $company->cnpj }}</div>
            </div>
            <div class="signature-cell">
                <div class="signature-line">Cliente</div>
                <div class="signature-detail">{{ $state['content'][0]['customer_name'] ?? '' }}</div>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            &copy; {{ date('Y') }} {{ $company->trade_name }}. Todos os direitos reservados.
        </div>
    </div>
</body>

</html>
