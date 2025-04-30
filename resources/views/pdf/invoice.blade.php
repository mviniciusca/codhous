<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Orçamento {{ $state['code'] ?? '' }}</title>
    @vite('resources/css/app.css')
    <style>
        .watermark {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 72px;
            color: rgba(229, 231, 235, 0.3);
            pointer-events: none;
            z-index: -1;
            white-space: nowrap;
        }

        @page {
            margin: 0;
            padding: 0;
        }
    </style>
</head>

<body class="bg-white text-xs leading-relaxed">
    <!-- Marca d'água -->
    <div class="watermark">{{ env('APP_NAME','Concrete') }}</div>

    <div class="max-w-[210mm] mx-auto px-6 py-4">
        <!-- Header Section -->
        <div class="border-b pb-4">
            <div class="flex justify-between items-start gap-4">
                <!-- Logo -->
                @if(isset($layout) && $layout->logo)
                <div class="w-32">
                    <img src="{{ Storage::url($layout->logo) }}" alt="Logo" class="w-full h-auto object-contain">
                </div>
                @endif

                <div class="flex-1">
                    <h1 class="text-base font-medium">ORÇAMENTO #{{ $state['code'] ?? 'N/A' }}</h1>
                    <div class="text-xs text-gray-500">
                        {{ date('d/m/Y', strtotime($state['created_at'] ?? now())) }}
                    </div>
                </div>

                <div>
                    <span class="inline-flex px-2 py-1 rounded-sm text-xs font-medium
                        {{ $state['status']=='aprovado' ? 'bg-green-100 text-green-800' :
                           ($state['status']=='rejeitado' ? 'bg-red-100 text-red-800' :
                           'bg-amber-100 text-amber-800') }}">
                        {{ ucfirst($state['status'] ?? 'Pendente') }}
                    </span>
                </div>
            </div>

            <!-- Two Column Layout for Company and Client Info -->
            <div class="mt-4 grid grid-cols-2 gap-6">
                <div class="space-y-1">
                    <p class="text-lg font-medium text-gray-900">{{ env('APP_NAME','Concrete') }}</p>
                    <p class="text-gray-600 leading-tight">Rua Rio de Janeiro, 25</p>
                    <p class="text-gray-600 leading-tight">Rio de Janeiro, RJ</p>
                    <p class="text-gray-600 leading-tight">CNPJ: 54.012.200/0001-41</p>
                    <p class="text-gray-600 leading-tight">(21) 96613-4366</p>
                    <p class="text-gray-600 leading-tight">sac@codhous.app</p>
                </div>
                <div class="space-y-1">
                    <p class="text-gray-900 leading-tight">{{ $state['content'][0]['customer_name'] ?? 'N/A' }}</p>
                    <p class="text-gray-600 leading-tight">{{ $state['content'][0]['customer_email'] ?? 'N/A' }}</p>
                    <p class="text-gray-600 leading-tight">{{ $state['content'][0]['customer_phone'] ?? 'N/A' }}</p>
                    @if(isset($state['content'][0]['street']))
                    <p class="text-gray-600 leading-tight">
                        {{ $state['content'][0]['street'] }}, {{ $state['content'][0]['number'] }}
                        {{ $state['content'][0]['neighborhood'] }} - {{ $state['content'][0]['city'] }}/{{
                        $state['content'][0]['state'] }}
                        CEP: {{ $state['content'][0]['postcode'] }}
                    </p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Products Table -->
        <div class="mt-4">
            <p class="font-medium mb-2 text-sm">PRODUTOS E SERVIÇOS</p>
            <table class="w-full">
                <thead>
                    <tr class="border-b border-gray-200">
                        <th class="py-2 text-left font-medium text-gray-600 w-[5%]">#</th>
                        <th class="py-2 text-left font-medium text-gray-600 w-[30%]">Produto</th>
                        <th class="py-2 text-left font-medium text-gray-600 w-[20%]">Opção</th>
                        <th class="py-2 text-left font-medium text-gray-600 w-[15%]">Local</th>
                        <th class="py-2 text-left font-medium text-gray-600 w-[10%]">Quant.</th>
                        <th class="py-2 text-left font-medium text-gray-600 w-[10%]">Preço Un.</th>
                        <th class="py-2 text-right font-medium text-gray-600 w-[10%]">Subtotal</th>
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
                    <tr class="border-b border-gray-100">
                        <td class="py-2">{{ $index + 1 }}</td>
                        <td class="py-2">{{ $productName }}</td>
                        <td class="py-2">{{ $productOption ? $productOption->name : '-' }}</td>
                        <td class="py-2">{{ $location ? $location->name : '-' }}</td>
                        <td class="py-2">{{ $product['quantity'] ?? 0 }} m³</td>
                        <td class="py-2">{{ env('CURRENCY_SUFFIX','R$') }} {{ number_format(($product['price'] ??
                            0),2,',','.') }}</td>
                        <td class="py-2 text-right">{{ env('CURRENCY_SUFFIX','R$') }} {{
                            number_format(($product['subtotal'] ?? 0),2,',','.') }}</td>
                    </tr>
                    @endforeach

                    @if($totalItems == 0)
                    <tr>
                        <td colspan="7" class="py-2 text-center text-gray-500">Nenhum produto encontrado</td>
                    </tr>
                    @endif
                </tbody>
            </table>
        </div>

        <!-- Summary and Totals - Two Columns -->
        <div class="mt-6 grid grid-cols-2 gap-6">
            <!-- Left Column - Notes -->
            <div class="text-xs space-y-2">
                <div class="bg-gray-50 p-3 rounded">
                    <p class="font-medium mb-1">Observações:</p>
                    <ol class="pl-4 space-y-1 list-decimal text-gray-600">
                        <li>Este documento é apenas um orçamento e não possui valor fiscal.</li>
                        <li>Orçamento válido por 15 dias a partir da data de emissão.</li>
                        <li>Forma de pagamento a combinar.</li>
                        <li>Prazo de entrega a combinar após a confirmação do pedido.</li>
                    </ol>
                </div>
                <!-- Signatures -->
                <div class="mt-6 grid grid-cols-2 gap-12">
                    <div class="text-center">
                        <div class="border-t border-gray-300 pt-2">{{ env('APP_NAME','Concrete') }}</div>
                    </div>
                    <div class="text-center">
                        <div class="border-t border-gray-300 pt-2">Cliente</div>
                    </div>
                </div>
            </div>

            <!-- Right Column - Totals -->
            <div>
                <div class="border rounded p-3 space-y-1.5 ml-auto">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Quantidade Total:</span>
                        <span>{{ $state['content'][0]['quantity'] ?? 0 }} m³</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Taxa Adicional:</span>
                        <span>{{ env('CURRENCY_SUFFIX','R$') }} {{ number_format(($state['content'][0]['tax'] ??
                            0),2,',','.') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Desconto:</span>
                        <span>-{{ env('CURRENCY_SUFFIX','R$') }} {{ number_format(($state['content'][0]['discount'] ??
                            0),2,',','.') }}</span>
                    </div>
                    <div class="border-t border-gray-200 mt-2 pt-2 flex justify-between font-medium">
                        <span>TOTAL:</span>
                        <span>{{ env('CURRENCY_SUFFIX','R$') }} {{ number_format(($state['content'][0]['total'] ??
                            0),2,',','.') }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="mt-6 pt-4 border-t text-xs text-center text-gray-500">
            &copy; {{ date('Y') }} {{ env('APP_NAME','Concrete') }}. Todos os direitos reservados.
        </div>
    </div>
</body>

</html>
