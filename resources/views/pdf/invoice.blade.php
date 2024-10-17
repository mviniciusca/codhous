<html lang="en">

<head>
    <title>Invoice</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap"
        rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>

<body>

    <div class="px-2 py-8 max-w-xl mx-auto">
        <div class="flex items-center justify-between mb-8">
            <div class="flex items-center">
                <div class="text-gray-950">
                    <p class="font-semibold text-2xl">{{ env('APP_NAME') . ' ' . __('') }}</p>
                    <p>Rua Rio de Janeiro, 25 - Rio de Janeiro, RJ </p>
                    <p>CNPJ: 54012200000441/4000</p>
                    <p>Phone: (21) 966134366 • Email: sac@codhous.app</p>
                </div>

            </div>
            <div class="text-gray-700">
                <div class="font-bold text-xl mb-2 uppercase">{{ __('Budget') }}</div>
                <div class="text-sm">{{ date('d/m/Y H:i', strtotime($state['created_at'])) }}</div>
                <div class="text-sm">{{ __('Budget') . ': #' . $state['code']}} </div>
            </div>
        </div>
        <div class="pb-8 mb-8">
            <h2 class="text-2xl font-bold mb-4">{{ _('Budget to') }}:</h2>
            <div class="text-gray-700 mb-2">
                {{ $state['content']['customer_name'] ?? __('No Customer Name') }}
            </div>
            <div class="text-gray-700 mb-2">
                {{ $state['content']['street'] ? $state['content']['street'] . ', ' . $state['content']['number'] :
                __('No Address') }}
            </div>
            <div class="text-gray-700 mb-2">
                {{ $state['content']['city'] . ' - ' . $state['content']['state'] }}
            </div>
            <div class="text-gray-700">
                {{ $state['content']['customer_email'] }} •
                {{ $state['content']['customer_phone'] }}
            </div>
        </div>
        <table class="w-full text-left mb-8">
            <thead>
                <tr>
                    <th class="text-gray-700 font-bold uppercase py-2">{{ _('Description') }}</th>
                    <th class="text-gray-700 font-bold uppercase py-2">{{ __('Quantity') }}</th>
                    <th class="text-gray-700 font-bold uppercase py-2">{{ __('Price') }}</th>
                    <th class="text-gray-700 font-bold uppercase py-2">{{ __('Total') }}</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="py-4 text-gray-700">{!! $product_name['name'] !!}</td>
                    <td class="py-4 text-gray-700">{{ $state['content']['quantity'] . 'm³' }}</td>
                    <td class="py-4 text-gray-700">{{ env('CURRENCY_SUFFIX').' '.$state['content']['price'] }}</td>
                    <td class="py-4 text-gray-700">{{ env('CURRENCY_SUFFIX').' '.$state['content']['total'] }}</td>
                </tr>
            </tbody>
        </table>
        <hr class="my-2">
        <div class="text-right mb-8">
            <div class="text-gray-700 mr-2">{{ __('Tax') }}: </div>
            <div class="text-gray-700 mr-2">{{ env('CURRENCY_SUFFIX').' '. $state['content']['tax'] }}</div>
            <hr class="my-2">
            <div class="text-gray-700 mr-2">{{ __('Discount') }}: </div>
            <div class="text-gray-700 mr-2">{{ env('CURRENCY_SUFFIX').' '. $state['content']['discount'] }}</div>
            <hr class="my-2">
            <div class="text-gray-700 mr-2 font-bold">{{ __('Total') }}: </div>
            <div class="text-gray-700 font-bold">{{ env('CURRENCY_SUFFIX').' '.$state['content']['total'] }}</div>
        </div>

        <div class="border-t-1 border-gray-300 pt-8 mb-8">
            <div class="text-gray-700 mb-2">{{
                __('This is a Budget document. This don\'t replace a
                fiscal Documentation or Legal Contract') }}
            </div>
            <div class="text-gray-700">{{ env('APP_NAME') }}</div>
        </div>
    </div>

</body>

</html>

<!--
    "id" => 99
  "code" => "964"
  "status" => "done"
  "is_active" => true
  "content" => array:17 [▼
    "fck" => 40
    "area" => 5
    "city" => "New Shyanne"
    "state" => "FK"
    "number" => 5412982
    "street" => "902 Lesch Extension"
    "product" => 2
    "postcode" => "21279-462"
    "quantity" => 90
    "neighborhood" => "North Mikayla"
    "customer_name" => "Dr. Ola Wisoky Jr."
    "customer_email" => "aflatley@yahoo.com"
    "customer_phone" => "(86)37251-058"
    "total" => "0.00"
    "price" => "523.94"
    "tax" => null
    "discount" => null
  ]
  "deleted_at" => null
  "created_at" => "2024-07-14 22:02:32"
  "updated_at" => "2024-10-17T15:18:17.000000Z"
]
  !-->