<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Orçamento {{ $state['code'] ?? '' }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 20px;
        }

        .container {
            max-width: 100%;
            margin: 0 auto;
        }

        .header {
            text-align: center;
            padding-bottom: 20px;
            border-bottom: 2px solid #444;
            margin-bottom: 20px;
        }

        .logo {
            max-width: 200px;
            margin-bottom: 10px;
        }

        .company-details {
            margin-bottom: 10px;
            font-size: 14px;
        }

        .document-title {
            font-size: 22px;
            font-weight: bold;
            margin-bottom: 5px;
            color: #333;
        }

        .document-subtitle {
            font-size: 16px;
            margin-bottom: 15px;
            color: #555;
        }

        .info-section {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
        }

        .info-box {
            width: 48%;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            background-color: #f9f9f9;
        }

        .info-title {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 10px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
        }

        .info-content p {
            margin: 5px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }

        table th,
        table td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        table th {
            background-color: #f2f2f2;
            font-weight: bold;
        }

        table tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .totals-section {
            margin: 20px 0;
            text-align: right;
        }

        .totals-table {
            width: 40%;
            margin-left: auto;
        }

        .totals-table td {
            padding: 5px 10px;
        }

        .totals-table .total-row {
            font-weight: bold;
            font-size: 16px;
            border-top: 2px solid #444;
        }

        .footer {
            margin-top: 50px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            font-size: 12px;
            color: #777;
        }

        .note-box {
            margin-top: 20px;
            padding: 15px;
            border: 1px solid #ddd;
            background-color: #f9f9f9;
            border-radius: 5px;
        }

        .signature-section {
            margin-top: 60px;
            display: flex;
            justify-content: space-between;
        }

        .signature-box {
            width: 45%;
            text-align: center;
        }

        .signature-line {
            margin-top: 50px;
            border-top: 1px solid #000;
            padding-top: 5px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <div class="document-title">ORÇAMENTO #{{ $state['code'] ?? 'N/A' }}</div>
            <div class="document-subtitle">Data de emissão: {{ date('d/m/Y', strtotime($state['created_at'] ?? now()))
                }}</div>
            <div class="company-details">
                <strong>{{ env('APP_NAME', 'Concrete') }}</strong><br>
                Rua Rio de Janeiro, 25 - Rio de Janeiro, RJ<br>
                CNPJ: 54.012.200/0001-41<br>
                Tel: (21) 96613-4366 | E-mail: sac@codhous.app<br>
                www.codhous.app
            </div>
        </div>

        <div class="info-section">
            <div class="info-box">
                <div class="info-title">DADOS DO CLIENTE</div>
                <div class="info-content">
                    <p><strong>Nome:</strong> {{ $state['content'][0]['customer_name'] ?? 'N/A' }}</p>
                    <p><strong>E-mail:</strong> {{ $state['content'][0]['customer_email'] ?? 'N/A' }}</p>
                    <p><strong>Telefone:</strong> {{ $state['content'][0]['customer_phone'] ?? 'N/A' }}</p>
                    @if(isset($state['content'][0]['street']))
                    <p>
                        <strong>Endereço:</strong><br>
                        {{ $state['content'][0]['street'] ?? '' }}
                        {{ isset($state['content'][0]['number']) ? ', ' . $state['content'][0]['number'] : '' }}<br>
                        {{ $state['content'][0]['neighborhood'] ?? '' }} -
                        {{ $state['content'][0]['city'] ?? '' }}/{{ $state['content'][0]['state'] ?? '' }}<br>
                        CEP: {{ $state['content'][0]['postcode'] ?? '' }}
                    </p>
                    @endif
                </div>
            </div>

            <div class="info-box">
                <div class="info-title">DADOS DO ORÇAMENTO</div>
                <div class="info-content">
                    <p><strong>Número:</strong> #{{ $state['code'] ?? 'N/A' }}</p>
                    <p><strong>Data:</strong> {{ date('d/m/Y', strtotime($state['created_at'] ?? now())) }}</p>
                    <p><strong>Status:</strong> {{ ucfirst($state['status'] ?? 'Pendente') }}</p>
                    <p><strong>Validade:</strong> 15 dias ({{ date('d/m/Y', strtotime('+15 days',
                        strtotime($state['created_at'] ?? now()))) }})</p>
                </div>
            </div>
        </div>

        <h2>PRODUTOS E SERVIÇOS</h2>
        <table>
            <thead>
                <tr>
                    <th style="width: 5%">#</th>
                    <th style="width: 30%">Produto</th>
                    <th style="width: 20%">Opção</th>
                    <th style="width: 15%">Local</th>
                    <th style="width: 10%">Quant.</th>
                    <th style="width: 10%">Preço Un.</th>
                    <th style="width: 10%">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @php
                $products = $state['content'][0]['products'] ?? [];
                $productsList = [];
                $totalItems = 0;

                // Extract products from nested structure if needed
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
                    <td>{{ env('CURRENCY_SUFFIX', 'R$') }} {{ number_format(($product['price'] ?? 0), 2, ',', '.') }}
                    </td>
                    <td>{{ env('CURRENCY_SUFFIX', 'R$') }} {{ number_format(($product['subtotal'] ?? 0), 2, ',', '.') }}
                    </td>
                </tr>
                @endforeach

                @if($totalItems == 0)
                <tr>
                    <td colspan="7" style="text-align: center;">Nenhum produto encontrado</td>
                </tr>
                @endif
            </tbody>
        </table>

        <div class="totals-section">
            <table class="totals-table">
                <tr>
                    <td><strong>Quantidade Total:</strong></td>
                    <td>{{ $state['content'][0]['quantity'] ?? 0 }} m³</td>
                </tr>
                @php
                $subtotal = 0;
                foreach($productsList as $product) {
                $subtotal += floatval($product['subtotal'] ?? 0);
                }
                @endphp
                <tr>
                    <td><strong>Subtotal:</strong></td>
                    <td>{{ env('CURRENCY_SUFFIX', 'R$') }} {{ number_format($subtotal, 2, ',', '.') }}</td>
                </tr>
                <tr>
                    <td><strong>Taxa Adicional:</strong></td>
                    <td>{{ env('CURRENCY_SUFFIX', 'R$') }} {{ number_format(($state['content'][0]['tax'] ?? 0), 2, ',',
                        '.') }}</td>
                </tr>
                <tr>
                    <td><strong>Desconto:</strong></td>
                    <td>-{{ env('CURRENCY_SUFFIX', 'R$') }} {{ number_format(($state['content'][0]['discount'] ?? 0), 2,
                        ',', '.') }}</td>
                </tr>
                <tr class="total-row">
                    <td><strong>TOTAL:</strong></td>
                    <td>{{ env('CURRENCY_SUFFIX', 'R$') }} {{ number_format(($state['content'][0]['total'] ?? 0), 2,
                        ',', '.') }}</td>
                </tr>
            </table>
        </div>

        <div class="note-box">
            <strong>Observações:</strong>
            <ol>
                <li>Este documento é apenas um orçamento e não possui valor fiscal.</li>
                <li>Orçamento válido por 15 dias a partir da data de emissão.</li>
                <li>Forma de pagamento a combinar.</li>
                <li>Prazo de entrega a combinar após a confirmação do pedido.</li>
            </ol>
        </div>

        <div class="signature-section">
            <div class="signature-box">
                <div class="signature-line">{{ env('APP_NAME', 'Concrete') }}</div>
            </div>
            <div class="signature-box">
                <div class="signature-line">Cliente</div>
            </div>
        </div>

        <div class="footer">
            <p>&copy; {{ date('Y') }} {{ env('APP_NAME', 'Concrete') }}. Todos os direitos reservados.</p>
        </div>
    </div>
</body>

</html>
