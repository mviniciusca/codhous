<!-- Header Section -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 pb-6 border-b border-gray-200 dark:border-gray-700">
    <!-- Budget Info -->
    <div class="space-y-2">
        <h2 class="text-lg font-medium leading-6 text-gray-950 dark:text-white">
            Orçamento #{{ $getRecord()->code }}
        </h2>
        <div class="flex items-center gap-3">
            <span class="text-sm">
                {{ \Carbon\Carbon::parse($getRecord()->created_at)->format('d/m/Y H:i') }}
            </span>
            <x-filament::badge :color="match($getRecord()->status) {
                        'pending' => 'primary',
                        'on going' => 'warning',
                        'done' => 'success',
                        'ignored' => 'danger',
                        default => 'gray',
                    }" class="text-xs">
                {{ __($getRecord()->status) }}
            </x-filament::badge>
        </div>
    </div>

    <!-- Customer Info -->
    <div class="space-y-1">
        <div class="space-y-1 text-sm">
            <p>{{ data_get($getRecord(), 'content.customer_name') }}</p>
            <p>{{ data_get($getRecord(), 'content.customer_email') }}</p>
            <p>{{ data_get($getRecord(), 'content.customer_phone') }}</p>
        </div>
    </div>

    <!-- Delivery Address -->
    <div class="space-y-2">
        <div class="space-y-1 text-sm">
            <p>
                {{ data_get($getRecord(), 'content.street') }}, {{ data_get($getRecord(), 'content.number', 'S/N')
                }}
            </p>
            <p>{{ data_get($getRecord(), 'content.neighborhood') }}</p>
            <p>
                {{ data_get($getRecord(), 'content.city') }}/{{ data_get($getRecord(), 'content.state') }}
            </p>
            <p>CEP: {{ data_get($getRecord(), 'content.postcode') }}</p>
        </div>
    </div>
</div>

<!-- Products Section -->
<div class="py-6 space-y-4">
    <h3 class="text-xs font-medium uppercase tracking-wider">
        Produtos
    </h3>

    <div class="filament-tables-container">
        <div class="filament-tables-table-container overflow-x-auto relative">
            <table class="filament-tables-table w-full">
                <thead>
                    <tr>
                        <th class="w-2/5 px-4 py-2 whitespace-nowrap filament-tables-header-cell">
                            <span class="flex items-center gap-x-1 text-sm font-medium">
                                Produto
                            </span>
                        </th>
                        <th class="px-4 py-2 whitespace-nowrap filament-tables-header-cell">
                            <span class="flex items-center gap-x-1 text-sm font-medium">
                                Local
                            </span>
                        </th>
                        <th class="px-4 py-2 whitespace-nowrap text-end filament-tables-header-cell">
                            <span class="flex items-center justify-end gap-x-1 text-sm font-medium">
                                Quantidade
                            </span>
                        </th>
                        <th class="px-4 py-2 whitespace-nowrap text-end filament-tables-header-cell">
                            <span class="flex items-center justify-end gap-x-1 text-sm font-medium">
                                Preço Unit.
                            </span>
                        </th>
                        <th class="px-4 py-2 whitespace-nowrap text-end filament-tables-header-cell">
                            <span class="flex items-center justify-end gap-x-1 text-sm font-medium">
                                Subtotal
                            </span>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @php
                    $products = collect(data_get($getRecord(), 'content.products', []));
                    @endphp

                    @forelse($products as $product)
                    @php
                    $productModel = \App\Models\Product::find(data_get($product, 'product'));
                    $productOptionModel = \App\Models\ProductOption::find(data_get($product, 'product_option'));
                    $locationModel = \App\Models\Location::find(data_get($product, 'location'));
                    @endphp

                    <tr class="">
                        <td class="px-4 py-3 filament-tables-cell">
                            <div>
                                <span class="font-medium text-sm">
                                    {{ $productModel?->name ?? 'Produto não encontrado' }}
                                </span>
                                @if($productOptionModel)
                                <div class="text-sm">
                                    {{ $productOptionModel->name }}
                                </div>
                                @endif
                            </div>
                        </td>
                        <td class="px-4 py-3 filament-tables-cell">
                            <span class="text-sm">
                                {{ $locationModel?->name ?? 'Local não encontrado' }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-end whitespace-nowrap filament-tables-cell">
                            <span class="text-xs">
                                {{ number_format(data_get($product, 'quantity', 0), 2, ',', '.') }} m³
                            </span>
                        </td>
                        <td class="px-4 py-3 text-end whitespace-nowrap filament-tables-cell">
                            <span class="text-sm">
                                {{ env('CURRENCY_SUFFIX') }} {{ number_format(data_get($product, 'price', 0), 2, ',',
                                '.') }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-end whitespace-nowrap filament-tables-cell">
                            <span class="text-sm">
                                {{ env('CURRENCY_SUFFIX') }} {{ number_format(data_get($product, 'subtotal', 0), 2, ',',
                                '.') }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-4 py-3 whitespace-nowrap filament-tables-cell">
                            <div class="flex items-center justify-center text-sm">
                                Nenhum produto encontrado
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Totals Section -->
<div class="pt-6 border-t border-gray-200 dark:border-gray-700">
    <div class="flex justify-end">
        <div class="w-full md:w-80 space-y-2">
            <div class="flex justify-between py-1.5 text-sm">
                <span>Quantidade Total:</span>
                <span class="font-medium">{{ number_format(data_get($getRecord(), 'content.quantity', 0), 2, ',',
                    '.') }} m³</span>
            </div>

            @if(data_get($getRecord(), 'content.tax', 0) > 0)
            <div class="flex justify-between py-1.5 text-sm">
                <span>Taxa:</span>
                <span class="font-medium">+ {{ env('CURRENCY_SUFFIX') }} {{ number_format(data_get($getRecord(),
                    'content.tax', 0), 2, ',', '.') }}</span>
            </div>
            @endif

            @if(data_get($getRecord(), 'content.discount', 0) > 0)
            <div class="flex justify-between py-1.5 text-sm">
                <span>Desconto:</span>
                <span class="font-medium">- {{ env('CURRENCY_SUFFIX') }} {{ number_format(data_get($getRecord(),
                    'content.discount', 0), 2, ',', '.') }}</span>
            </div>
            @endif

            <div
                class="flex justify-between pt-3 mt-2 border-t border-gray-200 dark:border-gray-700 text-base font-semibold">
                <span>Total:</span>
                <span>{{ env('CURRENCY_SUFFIX') }} {{ number_format(data_get($getRecord(), 'content.total', 0), 2,
                    ',', '.') }}</span>
            </div>
        </div>
    </div>
</div>
