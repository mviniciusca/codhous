<!-- Professional Invoice Header -->
<div class="pb-8 mb-12">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
        <div>
            <div class="flex items-center gap-3 mb-2">
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
                    Orçamento
                </h1>
                <x-filament::badge :color="match($getRecord()->status) {
                    'pending' => 'warning',
                    'on going' => 'info',
                    'done' => 'success',
                    'ignored' => 'danger',
                    default => 'gray',
                }" size="lg" class="text-xs font-semibold">
                    {{ __(ucfirst($getRecord()->status)) }}
                </x-filament::badge>
            </div>
            <p class="text-gray-600 dark:text-gray-400 text-sm font-medium">
                #{{ $getRecord()->code }}
            </p>
        </div>
        <div class="text-right">
            <p class="text-gray-600 dark:text-gray-400 text-sm mb-1">Data de Emissão</p>
            <p class="text-gray-900 dark:text-white text-lg font-semibold">
                {{ \Carbon\Carbon::parse($getRecord()->created_at)->format('d/m/Y') }}
            </p>
            <p class="text-gray-500 dark:text-gray-500 text-xs">
                {{ \Carbon\Carbon::parse($getRecord()->created_at)->format('H:i') }}
            </p>
        </div>
    </div>
</div>

<!-- Client and Delivery Information Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-12">
    <!-- Customer Card -->
    <div class="rounded-lg p-6">
        <div class="flex items-center gap-2 mb-4">
            <svg class="w-5 h-5 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
            </svg>
            <h3 class="text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wide">
                Informações do Cliente
            </h3>
        </div>
        <div class="space-y-3">
            <div class="flex items-start gap-3">
                <svg class="w-4 h-4 text-gray-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider">Nome</p>
                    <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ data_get($getRecord(),
                        'content.customer_name') }}</p>
                </div>
            </div>
            <div class="flex items-start gap-3">
                <svg class="w-4 h-4 text-gray-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider">E-mail</p>
                    <p class="text-sm font-medium text-gray-900 dark:text-white">{{ data_get($getRecord(),
                        'content.customer_email') }}</p>
                </div>
            </div>
            <div class="flex items-start gap-3">
                <svg class="w-4 h-4 text-gray-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                </svg>
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider">Telefone</p>
                    <p class="text-sm font-medium text-gray-900 dark:text-white">{{ data_get($getRecord(),
                        'content.customer_phone') }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Delivery Address Card -->
    <div class="rounded-lg p-6">
        <div class="flex items-center gap-2 mb-4">
            <svg class="w-5 h-5 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
            <h3 class="text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wide">
                Endereço de Entrega
            </h3>
        </div>
        <div class="space-y-2">
            <p class="text-sm font-medium text-gray-900 dark:text-white">
                {{ data_get($getRecord(), 'content.street') }}, {{ data_get($getRecord(), 'content.number', 'S/N') }}
            </p>
            <p class="text-sm text-gray-600 dark:text-gray-400">
                {{ data_get($getRecord(), 'content.neighborhood') }}
            </p>
            <p class="text-sm text-gray-600 dark:text-gray-400">
                {{ data_get($getRecord(), 'content.city') }} - {{ data_get($getRecord(), 'content.state') }}
            </p>
            <div class="flex items-center gap-2 pt-2">
                <span
                    class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-medium text-gray-900 dark:text-white">
                    CEP: {{ data_get($getRecord(), 'content.postcode') }}
                </span>
            </div>
        </div>
    </div>
</div>

<!-- Products Section -->
<div class="mb-12 pb-12">
    <div class="flex items-center gap-2 mb-4 py-3">
        <svg class="w-5 h-5 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
        </svg>
        <h3 class="text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wide">
            Produtos e Serviços
        </h3>
    </div>

    <div class="border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="">
                        <th class="px-6 py-3 text-left">
                            <span class="text-xs font-bold uppercase tracking-wider text-gray-600 dark:text-gray-400">
                                Produto / Serviço
                            </span>
                        </th>
                        <th class="px-6 py-3 text-left">
                            <span class="text-xs font-bold uppercase tracking-wider text-gray-600 dark:text-gray-400">
                                Local
                            </span>
                        </th>
                        <th class="px-6 py-3 text-right">
                            <span class="text-xs font-bold uppercase tracking-wider text-gray-600 dark:text-gray-400">
                                Quantidade
                            </span>
                        </th>
                        <th class="px-6 py-3 text-right">
                            <span class="text-xs font-bold uppercase tracking-wider text-gray-600 dark:text-gray-400">
                                Valor Unitário
                            </span>
                        </th>
                        <th class="px-6 py-3 text-right">
                            <span class="text-xs font-bold uppercase tracking-wider text-gray-600 dark:text-gray-400">
                                Subtotal
                            </span>
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @php
                    $products = collect(data_get($getRecord(), 'content.products', []));
                    $subtotalSum = 0;
                    @endphp

                    @forelse($products as $index => $product)
                    @php
                    $productModel = \App\Models\Product::find(data_get($product, 'product'));
                    $productOptionModel = \App\Models\ProductOption::find(data_get($product, 'product_option'));
                    $locationModel = \App\Models\Location::find(data_get($product, 'location'));
                    $subtotal = data_get($product, 'subtotal', 0);
                    $subtotalSum += $subtotal;
                    @endphp

                    <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-800/50 transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-start gap-3">
                                <div class="flex-shrink-0 w-8 h-8 rounded flex items-center justify-center">
                                    <span class="text-xs font-semibold text-gray-600 dark:text-gray-400">{{ $index + 1
                                        }}</span>
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-900 dark:text-white text-sm">
                                        {{ $productModel?->name ?? 'Produto não encontrado' }}
                                    </p>
                                    @if($productOptionModel)
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                        {{ $productOptionModel->name }}
                                    </p>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-sm font-medium text-gray-900 dark:text-white">
                                {{ $locationModel?->name ?? 'N/A' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <span class="text-sm font-semibold text-gray-900 dark:text-white">
                                {{ number_format(data_get($product, 'quantity', 0), 2, ',', '.') }} m³
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <span class="text-sm font-medium text-gray-900 dark:text-white">
                                {{ env('CURRENCY_SUFFIX') }} {{ number_format(data_get($product, 'price', 0), 2, ',',
                                '.') }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <span class="text-sm font-bold text-gray-900 dark:text-white">
                                {{ env('CURRENCY_SUFFIX') }} {{ number_format($subtotal, 2, ',', '.') }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12">
                            <div class="flex flex-col items-center justify-center text-center">
                                <svg class="w-16 h-16 text-gray-300 dark:text-gray-600 mb-4" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                                </svg>
                                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                    Nenhum produto encontrado neste orçamento
                                </p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
                @if($products->isNotEmpty())
                <tfoot class="">
                    <tr>
                        <td colspan="4" class="px-6 py-4 text-right">
                            <span class="text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wide">
                                Subtotal dos Produtos:
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <span class="text-base font-bold text-gray-900 dark:text-white">
                                {{ env('CURRENCY_SUFFIX') }} {{ number_format($subtotalSum, 2, ',', '.') }}
                            </span>
                        </td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>
</div>

<!-- Financial Summary Section -->
<div class="mb-12 pb-12">
    <div class="rounded-lg p-6">
        <div class="flex items-center gap-2 mb-6 py-3">
            <svg class="w-5 h-5 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
            </svg>
            <h3 class="text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wide">
                Resumo Financeiro
            </h3>
        </div>

        <div class="flex flex-wrap gap-4">
            <!-- Total Quantity -->
            <div class="flex-1 min-w-[180px] border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                <div class="flex flex-col items-center text-center gap-2">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Quantidade
                            Total</p>
                        <p class="text-lg font-bold text-gray-900 dark:text-white">
                            {{ number_format(data_get($getRecord(), 'content.quantity', 0), 2, ',', '.') }} m³
                        </p>
                    </div>
                </div>
            </div>

            <!-- Shipping -->
            @if(data_get($getRecord(), 'content.shipping', 0) > 0)
            <div class="flex-1 min-w-[180px] border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                <div class="flex flex-col items-center text-center gap-2">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0" />
                    </svg>
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Frete</p>
                        <p class="text-lg font-bold text-gray-900 dark:text-white">
                            {{ env('CURRENCY_SUFFIX') }} {{ number_format(data_get($getRecord(), 'content.shipping', 0),
                            2,
                            ',', '.') }}
                        </p>
                    </div>
                </div>
            </div>
            @endif

            <!-- Tax -->
            @if(data_get($getRecord(), 'content.tax', 0) > 0)
            <div class="flex-1 min-w-[180px] border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                <div class="flex flex-col items-center text-center gap-2">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Taxas</p>
                        <p class="text-lg font-bold text-gray-900 dark:text-white">
                            + {{ env('CURRENCY_SUFFIX') }} {{ number_format(data_get($getRecord(), 'content.tax', 0), 2,
                            ',', '.') }}
                        </p>
                    </div>
                </div>
            </div>
            @endif

            <!-- Discount -->
            @if(data_get($getRecord(), 'content.discount', 0) > 0)
            <div class="flex-1 min-w-[180px] border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                <div class="flex flex-col items-center text-center gap-2">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                    </svg>
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Desconto</p>
                        <p class="text-lg font-bold text-gray-900 dark:text-white">
                            - {{ env('CURRENCY_SUFFIX') }} {{ number_format(data_get($getRecord(), 'content.discount',
                            0),
                            2, ',', '.') }}
                        </p>
                    </div>
                </div>
            </div>
            @endif

            <!-- Total -->
            <div class="flex-1 min-w-[180px] border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                <div class="flex flex-col items-center text-center gap-2">
                    <svg class="w-5 h-5 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <div>
                        <p class="text-xs font-bold text-gray-900 dark:text-white uppercase tracking-wider mb-1">Valor
                            Total</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">
                            {{ env('CURRENCY_SUFFIX') }} {{ number_format(data_get($getRecord(), 'content.total', 0), 2,
                            ',', '.') }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Footer Notes -->
<div class="rounded-lg p-6">
    <div class="flex items-start gap-3 py-3">
        <div class="flex-shrink-0">
            <svg class="w-6 h-6 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
        </div>
        <div class="flex-1">
            <h4 class="text-sm font-bold text-gray-900 dark:text-white mb-2 uppercase tracking-wide">
                Observações Importantes
            </h4>
            <ul class="space-y-1 text-xs text-gray-600 dark:text-gray-400">
                <li class="flex items-start gap-2">
                    <span class="text-gray-400 mt-0.5">•</span>
                    <span>Este orçamento é válido por 30 dias a partir da data de emissão</span>
                </li>
                <li class="flex items-start gap-2">
                    <span class="text-gray-400 mt-0.5">•</span>
                    <span>Os valores apresentados podem sofrer alterações sem aviso prévio</span>
                </li>
                <li class="flex items-start gap-2">
                    <span class="text-gray-400 mt-0.5">•</span>
                    <span>Para confirmar este orçamento, entre em contato através dos nossos canais oficiais</span>
                </li>
            </ul>
        </div>
    </div>
</div>
