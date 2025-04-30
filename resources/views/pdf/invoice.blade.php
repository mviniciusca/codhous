<html lang="en">

<head>
    <title>Nota Fiscal</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap"
        rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            font-size: 9pt;
            line-height: 1.2;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 100%;
            max-width: 800px;
            margin: 0 auto;
            padding: 10px;
        }

        .header {
            border-bottom: 1px solid #000;
            padding-bottom: 8px;
            margin-bottom: 10px;
            text-align: center;
        }

        .company-name {
            font-size: 14pt;
            font-weight: bold;
            margin-bottom: 2px;
        }

        .company-details {
            font-size: 8pt;
        }

        .document-title {
            font-size: 11pt;
            font-weight: bold;
            text-align: center;
            margin: 8px 0;
            text-transform: uppercase;
        }

        .customer-section {
            border: 1px solid #000;
            padding: 5px;
            margin-bottom: 10px;
            font-size: 8pt;
        }

        .section-title {
            font-weight: bold;
            font-size: 9pt;
            margin-bottom: 4px;
            text-transform: uppercase;
        }

        .customer-info {
            margin-bottom: 2px;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
            font-size: 8pt;
        }

        .items-table th,
        .items-table td {
            border: 1px solid #000;
            padding: 4px;
            text-align: left;
        }

        .items-table th {
            background-color: #f0f0f0;
            font-size: 8pt;
            font-weight: bold;
        }

        .totals-section {
            text-align: right;
            margin-top: 5px;
            border-top: 1px dashed #000;
            padding-top: 5px;
            font-size: 8pt;
        }

        .total-line {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 2px;
        }

        .total-label {
            font-weight: normal;
            margin-right: 15px;
        }

        .total-value {
            width: 100px;
            text-align: right;
        }

        .grand-total {
            font-weight: bold;
            font-size: 10pt;
            margin-top: 5px;
            border-top: 1px solid #000;
            padding-top: 2px;
        }

        .footer {
            margin-top: 15px;
            padding-top: 5px;
            border-top: 1px solid #000;
            font-size: 7pt;
            text-align: center;
        }

        .legal-text {
            font-size: 7pt;
            margin: 5px 0;
        }

        .dotted-line {
            border-top: 1px dotted #000;
            margin: 10px 0;
        }

        /* Marca d'água */
        .watermark {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1000;
            display: flex;
            justify-content: center;
            align-items: center;
            pointer-events: none;
        }

        .watermark-text {
            color: rgba(200, 200, 200, 0.2);
            font-size: 120px;
            font-weight: bold;
            transform: rotate(-45deg);
            white-space: nowrap;
            user-select: none;
        }
    </style>
</head>

<body>
    <!-- Marca d'água -->
    <div class="watermark">
        <div class="watermark-text">CODHOUS</div>
    </div>

    <div class="container">
        <div class="header">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <div style="width: 60%; text-align: left;">
                    <div class="company-name">{{ env('APP_NAME') }}</div>
                    <div class="company-details">
                        Rua Rio de Janeiro, 25 - Rio de Janeiro, RJ • CNPJ: 54012200000441/4000<br>
                        Tel: (21) 966134366 • Email: sac@codhous.app
                    </div>
                </div>
                <div style="width: 38%; text-align: right;">
                    <div class="document-title">Orçamento Nº {{ $state['code'] }}</div>
                    <div style="font-size: 8pt; margin-top: 4px;">
                        Data: {{ date('d/m/Y H:i', strtotime($state['created_at'])) }}
                    </div>
                </div>
            </div>
        </div>

        <div class="customer-section">
            <div style="display: flex; justify-content: space-between;">
                <div style="width: 48%;">
                    <div class="customer-info"><strong>Nome:</strong> {{ $state['content'][0]['customer_name'] ?? 'N/A'
                        }}</div>
                    <div class="customer-info"><strong>Endereço:</strong> {{ isset($state['content'][0]['street']) ?
                        $state['content'][0]['street'] . ', ' . $state['content'][0]['number'] : 'N/A' }}</div>
                    <div class="customer-info"><strong>Bairro:</strong> {{ $state['content'][0]['neighborhood'] ?? 'N/A'
                        }}</div>
                    <div class="customer-info"><strong>Cidade/UF:</strong> {{ ($state['content'][0]['city'] ?? '') . ' -
                        ' .
                        ($state['content'][0]['state'] ?? '') }}</div>
                </div>
                <div style="width: 48%;">
                    <div class="customer-info"><strong>CEP:</strong> {{ $state['content'][0]['postcode'] ?? 'N/A' }}
                    </div>
                    <div class="customer-info"><strong>Email:</strong> {{ $state['content'][0]['customer_email'] ??
                        'N/A' }}</div>
                    <div class="customer-info"><strong>Telefone:</strong> {{ $state['content'][0]['customer_phone'] ??
                        'N/A' }}</div>
                </div>
            </div>
        </div>

        <table class="items-table">
            <thead>
                <tr>
                    <th style="width: 40%;">Descrição</th>
                    <th style="width: 15%;">Qtd</th>
                    <th style="width: 20%;">Preço Unit.</th>
                    <th style="width: 25%;">Total</th>
                </tr>
            </thead>
            <tbody>
                @php
                $subtotal = 0;
                $productList = [];

                // Função recursiva para encontrar todos os produtos na estrutura aninhada
                function findProducts($array, &$products) {
                if (!is_array($array)) return;

                // Se este item tem um 'product', é um produto válido
                if (isset($array['product'])) {
                $products[] = $array;
                return;
                }

                // Caso contrário, procurar recursivamente em todos os elementos
                foreach ($array as $item) {
                if (is_array($item)) {
                findProducts($item, $products);
                }
                }
                }

                // Buscar produtos na estrutura de conteúdo
                if (isset($state['content'][0]['products'])) {
                findProducts($state['content'][0]['products'], $productList);
                }

                // Calcular subtotal
                foreach ($productList as $product) {
                $subtotal += (float)($product['subtotal'] ?? 0);
                }
                @endphp

                @if(count($productList) > 0)
                @foreach($productList as $product)
                @php
                $productModel = \App\Models\Product::find($product['product']);
                $productOption = isset($product['product_option']) ?
                \App\Models\ProductOption::find($product['product_option']) : null;
                $location = isset($product['location']) ? \App\Models\Location::find($product['location']) : null;
                @endphp
                <tr>
                    <td>
                        {{ $productModel->name ?? 'Produto' }}
                        @if($productOption)
                        <br><small>Opção: {{ $productOption->name }}</small>
                        @endif
                        @if($location)
                        <br><small>Local: {{ $location->name }}</small>
                        @endif
                    </td>
                    <td>{{ $product['quantity'] ?? '0' }}m³</td>
                    <td>{{ env('CURRENCY_SUFFIX').' '.($product['price'] ?? '0') }}</td>
                    <td>{{ env('CURRENCY_SUFFIX').' '.($product['subtotal'] ?? '0') }}</td>
                </tr>
                @endforeach
                @else
                <tr>
                    <td colspan="4" style="text-align: center;">Nenhum produto encontrado</td>
                </tr>
                @endif

                <tr>
                    <td colspan="4" style="height: 20px; border-bottom: 1px dashed #000;"></td>
                </tr>
            </tbody>
        </table>

        <div class="totals-section">
            <div class="total-line">
                <div class="total-label">Subtotal:</div>
                <div class="total-value">{{ env('CURRENCY_SUFFIX').' '.($subtotal ?? '0') }}</div>
            </div>
            <div class="total-line">
                <div class="total-label">Taxa ({{ $state['content'][0]['tax'] ?? '0' }}%):</div>
                <div class="total-value">{{ env('CURRENCY_SUFFIX').' '. ($state['content'][0]['tax'] ?? '0') }}</div>
            </div>
            <div class="total-line">
                <div class="total-label">Desconto ({{ $state['content'][0]['discount'] ?? '0' }}%):</div>
                <div class="total-value">{{ env('CURRENCY_SUFFIX').' '. ($state['content'][0]['discount'] ?? '0') }}
                </div>
            </div>
            <div class="total-line grand-total">
                <div class="total-label">TOTAL:</div>
                <div class="total-value">{{ env('CURRENCY_SUFFIX').' '.($state['content'][0]['total'] ?? '0') }}</div>
            </div>
        </div>

        <div class="dotted-line"></div>

        <div style="font-size: 7pt; margin-bottom: 10px;">
            <strong>Forma de Pagamento:</strong> A combinar
        </div>

        <div class="legal-text">
            Este documento é apenas um orçamento e não possui valor fiscal.<br>
            Orçamento válido por 15 dias a partir da data de emissão.
        </div>

        <div class="footer">
            {{ env('APP_NAME') }} • {{ date('d/m/Y') }}<br>
            www.codhous.app
        </div>
    </div>
</body>

</html>
