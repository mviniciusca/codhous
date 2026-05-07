<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Orçamento {{ $state['code'] ?? '' }}</title>
    <style>
        @page {
            margin: 1cm;
        }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 8px;
            line-height: 1.2;
            color: #000000;
            background-color: #ffffff;
            margin: 0;
            padding: 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: -1px; /* Overlap borders */
        }
        td {
            border: 1px solid #000000;
            padding: 2px 4px;
            vertical-align: top;
        }
        .label {
            display: block;
            font-size: 6px;
            text-transform: uppercase;
            font-weight: bold;
            margin-bottom: 1px;
        }
        .value {
            display: block;
            font-size: 9px;
            font-weight: normal;
        }
        .bold {
            font-weight: bold;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        
        /* --- HEADER --- */
        .header-logo {
            width: 30%;
            text-align: center;
            vertical-align: middle;
        }
        .header-company {
            width: 40%;
        }
        .header-danfe {
            width: 30%;
            text-align: center;
        }
        .logo-img {
            max-width: 150px;
            max-height: 60px;
        }

        /* --- SECTIONS --- */
        .section-title {
            background-color: #f3f4f6;
            font-weight: bold;
            padding: 3px 5px;
            border: 1px solid #000000;
            text-transform: uppercase;
            font-size: 7px;
        }

        /* --- PRODUCTS TABLE --- */
        .products-table th {
            border: 1px solid #000000;
            background-color: #f3f4f6;
            font-size: 7px;
            text-transform: uppercase;
            padding: 3px;
        }
        .products-table td {
            font-size: 8px;
            padding: 3px;
        }

        /* --- TOTALS --- */
        .total-box {
            background-color: #f3f4f6;
            font-weight: bold;
        }
        
        .footer {
            margin-top: 10px;
            font-size: 6px;
            text-align: center;
        }
    </style>
</head>

<body>
    <!-- Cabeçalho Principal -->
    <table>
        <tr>
            <td class="header-logo" rowspan="2">
                @php
                    $logoPath = data_get($company, 'invoice_logo');
                    $fullLogoPath = $logoPath ? storage_path('app/public/' . $logoPath) : null;
                @endphp
                @if($fullLogoPath && file_exists($fullLogoPath))
                    <img src="{{ $fullLogoPath }}" class="logo-img">
                @else
                    <div style="font-size: 14px; font-weight: bold;">{{ $company->trade_name ?? 'LOGO' }}</div>
                @endif
            </td>
            <td class="header-company">
                <span class="label">Emitente</span>
                <span class="value bold">{{ $company->legal_name ?? $company->trade_name }}</span>
                <span class="value">
                    {{ data_get($company, 'address.street') }}, {{ data_get($company, 'address.number') }}<br>
                    {{ data_get($company, 'address.neighborhood') }} - {{ data_get($company, 'address.city') }}/{{ data_get($company, 'address.state') }}<br>
                    CEP: {{ data_get($company, 'address.postcode') }} - Tel: {{ $company->phone }}
                </span>
            </td>
            <td class="header-danfe">
                <span style="font-size: 12px; font-weight: bold;">ORÇAMENTO</span>
                <br>
                <span style="font-size: 10px;">Nº {{ $state['code'] ?? '0000' }}</span>
                <br>
                <span style="font-size: 8px;">FOLHA 1 / 1</span>
            </td>
        </tr>
        <tr>
            <td>
                <table style="margin: -2px -4px;">
                    <tr>
                        <td style="border: none; width: 50%;">
                            <span class="label">CNPJ / CPF</span>
                            <span class="value">{{ $company->document }}</span>
                        </td>
                        <td style="border: none; border-left: 1px solid #000; width: 50%;">
                            <span class="label">Inscrição Estadual</span>
                            <span class="value">{{ $company->ie ?? '-' }}</span>
                        </td>
                    </tr>
                </table>
            </td>
            <td class="text-center" style="vertical-align: middle;">
                <span class="label">Data de Emissão</span>
                <span class="value">{{ date('d/m/Y', strtotime($state['created_at'] ?? now())) }}</span>
            </td>
        </tr>
    </table>

    <!-- Dados do Cliente -->
    <div class="section-title">Destinatário / Remetente</div>
    <table>
        <tr>
            <td colspan="3" style="width: 70%;">
                <span class="label">Nome / Razão Social</span>
                <span class="value">{{ data_get($state['content'], 'customer_name', 'Consumidor Final') }}</span>
            </td>
            <td style="width: 30%;">
                <span class="label">CNPJ / CPF</span>
                <span class="value">{{ data_get($state['content'], 'customer_document', '-') }}</span>
            </td>
        </tr>
        <tr>
            <td style="width: 50%;">
                <span class="label">Endereço</span>
                <span class="value">{{ data_get($state['content'], 'street', '-') }}, {{ data_get($state['content'], 'number', 'S/N') }}</span>
            </td>
            <td style="width: 25%;">
                <span class="label">Bairro / Distrito</span>
                <span class="value">{{ data_get($state['content'], 'neighborhood', '-') }}</span>
            </td>
            <td style="width: 15%;">
                <span class="label">CEP</span>
                <span class="value">{{ data_get($state['content'], 'postcode', '-') }}</span>
            </td>
            <td style="width: 10%;">
                <span class="label">Data Saída</span>
                <span class="value">{{ date('d/m/Y') }}</span>
            </td>
        </tr>
        <tr>
            <td style="width: 50%;">
                <span class="label">Município</span>
                <span class="value">{{ data_get($state['content'], 'city', '-') }}</span>
            </td>
            <td style="width: 10%;">
                <span class="label">UF</span>
                <span class="value text-center">{{ data_get($state['content'], 'state', '-') }}</span>
            </td>
            <td colspan="2" style="width: 40%;">
                <span class="label">Telefone / WhatsApp</span>
                <span class="value">{{ data_get($state['content'], 'customer_phone', '-') }}</span>
            </td>
        </tr>
    </table>

    <!-- Cálculo do Imposto / Totais -->
    <div class="section-title">Cálculo do Orçamento</div>
    <table>
        <tr>
            <td style="width: 18%;">
                <span class="label">Subtotal Itens</span>
                <span class="value text-right">{{ number_format(floatval(data_get($state['content'], 'subtotal', 0)), 2, ',', '.') }}</span>
            </td>
            <td style="width: 18%;">
                <span class="label">Valor do Frete</span>
                <span class="value text-right">{{ number_format(floatval(data_get($state['content'], 'shipping', 0)), 2, ',', '.') }}</span>
            </td>
            <td style="width: 18%;">
                <span class="label">Taxas / Outros</span>
                <span class="value text-right">{{ number_format(floatval(data_get($state['content'], 'tax', 0)), 2, ',', '.') }}</span>
            </td>
            <td style="width: 18%;">
                <span class="label">(-) Desconto</span>
                <span class="value text-right">{{ number_format(floatval(data_get($state['content'], 'discount', 0)), 2, ',', '.') }}</span>
            </td>
            <td style="width: 28%;" class="total-box">
                <span class="label">Valor Total do Orçamento</span>
                <span class="value text-right bold" style="font-size: 11px;">R$ {{ number_format(floatval(data_get($state['content'], 'total', 0)), 2, ',', '.') }}</span>
            </td>
        </tr>
    </table>

    <!-- Itens do Orçamento -->
    <div class="section-title">Dados dos Produtos / Serviços</div>
    <table class="products-table">
        <thead>
            <tr>
                <th style="width: 10%;">Cód. Prod.</th>
                <th style="width: 40%;">Descrição do Produto / Serviço</th>
                <th style="width: 10%;">NCM/SH</th>
                <th style="width: 5%;">CST</th>
                <th style="width: 5%;">CFOP</th>
                <th style="width: 5%;">UNID.</th>
                <th style="width: 5%;">QTD.</th>
                <th style="width: 10%;">V. UNIT.</th>
                <th style="width: 10%;">V. TOTAL</th>
            </tr>
        </thead>
        <tbody>
            @php
                $items = $budget->budgetItems()->with(['product', 'productOption', 'location'])->get();
            @endphp
            @foreach($items as $item)
                <tr>
                    <td class="text-center">{{ $item->product_id ?? '-' }}</td>
                    <td>
                        {{ $item->product?->name ?? 'Produto' }}
                        @if($item->productOption) ({{ $item->productOption->name }}) @endif
                        @if($item->location) - Local: {{ $item->location->name }} @endif
                    </td>
                    <td class="text-center">0000.00.00</td>
                    <td class="text-center">000</td>
                    <td class="text-center">5102</td>
                    <td class="text-center">{{ $item->productOption?->unit?->value ?? 'UN' }}</td>
                    <td class="text-center">{{ $item->productOption?->unit?->isDecimal() ? number_format($item->quantity, 2, '.', ',') : number_format($item->quantity, 0, '.', ',') }}</td>
                    <td class="text-right">{{ number_format($item->price, 2, '.', ',') }}</td>
                    <td class="text-right">{{ number_format($item->subtotal, 2, '.', ',') }}</td>
                </tr>
            @endforeach
            @for($i = count($items); $i < 10; $i++)
                <tr>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                </tr>
            @endfor
        </tbody>
    </table>

    <!-- Dados Adicionais -->
    <div class="section-title">Dados Adicionais</div>
    <table>
        <tr>
            <td style="width: 70%; height: 60px;">
                <span class="label">Informações Complementares</span>
                <span class="value" style="font-size: 7px;">
                    {{ $company->budget_information }}
                    <br><br>
                    <strong>OBSERVAÇÕES:</strong> {{ data_get($state['content'], 'observation', 'Nenhuma') }}
                </span>
            </td>
            <td style="width: 30%;">
                <span class="label">Reservado ao Fisco</span>
            </td>
        </tr>
    </table>

    <div class="footer">
        Este documento é uma proposta comercial e não possui valor de nota fiscal eletrônica.<br>
        Gerado em {{ date('d/m/Y H:i') }} &bull; {{ $company->trade_name }}
    </div>
</body>
</html>