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
                    <p class="font-semibold text-3xl">{{ env('APP_NAME') . ' ' . __('') }}</p>
                    <p>Rua Rio de Janeiro, 25 - Rio de Janeiro, RJ </p>
                    <p>CNPJ: 54012200000441/4000</p>
                    <p>Phone:(21) 966134366 • Email: sac@codhous.app</p>
                </div>

            </div>
            <div class="text-gray-700">
                <div class="font-bold text-xl mb-2 uppercase">{{ __('Budget') }}</div>
                <div class="text-sm">{{ date('d/m/Y H:i', strtotime($state['created_at'])) }}</div>
                <div class="text-sm">{{ __('Budget') . ': #' }} </div>
            </div>
        </div>
        <div class="border-b-2 border-gray-300 pb-8 mb-8">
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
                    <th class="text-gray-700 font-bold uppercase py-2">Description</th>
                    <th class="text-gray-700 font-bold uppercase py-2">Quantity</th>
                    <th class="text-gray-700 font-bold uppercase py-2">Price</th>
                    <th class="text-gray-700 font-bold uppercase py-2">Total</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="py-4 text-gray-700">Product 1</td>
                    <td class="py-4 text-gray-700">1</td>
                    <td class="py-4 text-gray-700">$100.00</td>
                    <td class="py-4 text-gray-700">$100.00</td>
                </tr>
            </tbody>
        </table>
        <div class="flex justify-end mb-8">
            <div class="text-gray-700 mr-2">Subtotal:</div>
            <div class="text-gray-700">$425.00</div>
        </div>
        <div class="text-right mb-8">
            <div class="text-gray-700 mr-2">Tax:</div>
            <div class="text-gray-700">$25.50</div>

        </div>
        <div class="flex justify-end mb-8">
            <div class="text-gray-700 mr-2">Total:</div>
            <div class="text-gray-700 font-bold text-xl">$450.50</div>
        </div>
        <div class="border-t-2 border-gray-300 pt-8 mb-8">
            <div class="text-gray-700 mb-2">Payment is due within 30 days. Late payments are subject to fees.</div>
            <div class="text-gray-700 mb-2">Please make checks payable to Your Company Name and mail to:</div>
            <div class="text-gray-700">123 Main St., Anytown, USA 12345</div>
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