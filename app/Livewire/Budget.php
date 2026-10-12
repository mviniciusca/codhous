<?php

namespace App\Livewire;

use App\Mail\AdminNewBudgetMail;
use App\Mail\CustomerBudgetReceivedMail;
use App\Models\Budget as BudgetModel;
use App\Models\Product;
use App\Models\ProductOption;
use App\Models\User;
use App\Rules\CepInOperationAreaRule;
use App\Services\BudgetCalculatorService;
use App\Services\OperationAreaService;
use App\Services\PostcodeFinderService;
use App\Services\TurnstileService;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Locked;
use Livewire\Component;
use Livewire\WithFileUploads;

class Budget extends Component
{
    use WithFileUploads;

    /** Máximo de envios por IP dentro da janela abaixo */
    protected const SUBMIT_MAX_ATTEMPTS = 5;

    protected const SUBMIT_DECAY_SECONDS = 600;

    public const DELIVERY_PERIODS = [
        'manha' => 'Manhã',
        'tarde' => 'Tarde',
        'flexivel' => 'Flexível',
    ];

    public const PUMP_OPTIONS = [
        'sim' => 'Sim, preciso de bomba',
        'nao' => 'Não, descarga direta',
        'nao_sei' => 'Não sei, preciso de orientação',
    ];

    public ?string $bgColor = '';

    public ?string $title = null;

    public ?string $subtitle = null;

    public ?string $description = null;

    public bool $headerVisible = true;

    public string $headerAlignment = 'left';

    public bool $isSubmitted = false;

    public ?string $turnstileToken = null;

    // Wizard
    public int $currentStep = 1;

    // Repeater
    public array $items = [];

    // Form Data
    public string $location_id = '';

    public $photos = [];

    public string $customer_name = '';

    public string $customer_phone = '';

    public string $customer_email = '';

    public string $postcode = '';

    public string $street = '';

    public string $number = '';

    public string $complement = '';

    public string $neighborhood = '';

    public string $city = '';

    public string $state = '';

    // Entrega
    public string $delivery_date = '';

    public string $delivery_period = '';

    public string $needs_pump = '';

    public string $notes = '';

    // Derived (calculados no servidor, não podem ser alterados pelo navegador)
    #[Locked]
    public float $shipping = 0;

    #[Locked]
    public float $subtotal = 0;

    #[Locked]
    public float $total = 0;

    #[Locked]
    public float $totalQuantity = 0;

    #[Locked]
    public string $code = '';

    // Caches da requisição atual (não são persistidos entre requisições)
    protected array $productsCache = [];

    protected array $productOptionsCache = [];

    public function mount()
    {
        $this->addItem();
    }

    public function addItem()
    {
        $this->items[] = [
            'id' => uniqid(),
            'product_id' => '',
            'option_id' => '',
            'quantity' => 1,
            'price' => 0,
            'subtotal' => 0,
            'min_quantity' => 1,
            'unit' => '',
        ];
    }

    public function removeItem($index)
    {
        unset($this->items[$index]);
        $this->items = array_values($this->items);
        if (empty($this->items)) {
            $this->addItem();
        }
        $this->recalculateAll();
    }

    public function updatedItems($value, $name)
    {
        $parts = explode('.', $name);
        if (count($parts) === 2) {
            $index = $parts[0];
            $field = $parts[1];

            if (! isset($this->items[$index])) {
                return;
            }

            if ($field === 'product_id') {
                $this->items[$index]['option_id'] = '';
                $this->items[$index]['unit'] = '';

                $productId = $this->items[$index]['product_id'];
                if ($productId) {
                    $product = $this->getCachedProduct($productId);
                    $min = (float) ($product?->min_quantity ?? 1);
                    $this->items[$index]['min_quantity'] = $min;
                    $this->items[$index]['quantity'] = $min;
                }
            }

            if ($field === 'quantity') {
                $min = (float) ($this->items[$index]['min_quantity'] ?? 1);
                $qty = (float) ($this->items[$index]['quantity'] ?: 0);
                if ($qty < $min) {
                    $this->items[$index]['quantity'] = $min;
                }
            }

            // O preço e a unidade sempre vêm do banco, nunca do navegador
            $option = $this->getCachedProductOption($this->items[$index]['option_id'] ?? null);
            $this->items[$index]['price'] = (float) ($option?->price ?? 0);
            $this->items[$index]['unit'] = $option?->unit?->value ?? '';

            $qty = (float) ($this->items[$index]['quantity'] ?? 0);
            $this->items[$index]['subtotal'] = BudgetCalculatorService::calculateItemSubtotal($qty, $this->items[$index]['price']);

            $this->recalculateAll();
        }
    }

    public function updatedPostcode($value)
    {
        $this->street = '';
        $this->neighborhood = '';
        $this->city = '';
        $this->state = '';
        $this->shipping = 0;

        $cep = preg_replace('/\D/', '', $value);
        if (strlen($cep) === 8) {
            $postcodeService = new PostcodeFinderService($value, function ($key, $val) {
                $k = str_replace('content.', '', $key);
                if (in_array($k, ['street', 'neighborhood', 'city', 'state'], true)) {
                    $this->$k = $val ?? '';
                }
            });
            $postcodeService->find();

            $this->shipping = $this->shippingForPostcode($value);
            $this->recalculateAll();
        }
    }

    public function recalculateAll()
    {
        $result = BudgetCalculatorService::calculateTotal($this->formattedProducts(), $this->shipping, 0, 0);
        $this->totalQuantity = $result['quantity'];
        $this->subtotal = (float) $result['subtotal'];
        $this->total = (float) $result['total'];
    }

    public function setStep($step)
    {
        // Não permite pular etapas sem validar as anteriores
        if ($step >= 2) {
            $this->validate($this->itemRules(), $this->itemMessages());
        }

        if ($step >= 3) {
            $this->validate($this->contactRules(), $this->contactMessages());
        }

        $this->currentStep = $step;
    }

    public function submit(TurnstileService $turnstile)
    {
        $rateKey = 'budget-submit:'.request()->ip();
        if (RateLimiter::tooManyAttempts($rateKey, self::SUBMIT_MAX_ATTEMPTS)) {
            $minutes = (int) ceil(RateLimiter::availableIn($rateKey) / 60);
            $this->addError('submit', "Muitas solicitações enviadas. Tente novamente em {$minutes} minuto(s).");

            return;
        }

        // Revalida tudo: o método pode ser chamado diretamente, sem passar pelas etapas
        $this->validate(
            array_merge($this->itemRules(), $this->contactRules()),
            array_merge($this->itemMessages(), $this->contactMessages())
        );

        if ($turnstile->isEnabled() && ! $turnstile->verify($this->turnstileToken, request()->ip())) {
            $this->turnstileToken = null;
            $this->addError('turnstileToken', 'A verificação anti-spam falhou. Atualize a página.');
            $this->dispatch('budget-turnstile-reset');

            return;
        }

        RateLimiter::hit($rateKey, self::SUBMIT_DECAY_SECONDS);

        // Recalcula preços e frete a partir do banco antes de gravar
        $this->refreshPricesFromDatabase();
        $this->shipping = $this->shippingForPostcode($this->postcode);
        $this->recalculateAll();

        $content = [
            'products' => $this->formattedProducts(),
            'customer_name' => trim($this->customer_name),
            'customer_phone' => self::formatPhone($this->customer_phone),
            'customer_email' => trim($this->customer_email),
            'postcode' => $this->postcode,
            'street' => $this->street,
            'number' => trim($this->number),
            'complement' => trim($this->complement),
            'neighborhood' => $this->neighborhood,
            'city' => $this->city,
            'state' => $this->state,
            'delivery_date' => $this->delivery_date ?: null,
            'delivery_period' => $this->delivery_period ?: null,
            'needs_pump' => $this->needs_pump ?: null,
            'notes' => trim($this->notes),
            'shipping' => $this->shipping,
            'tax' => 0,
            'discount' => 0,
            'quantity' => $this->totalQuantity,
            'price' => $this->items[0]['price'] ?? 0,
            'subtotal' => $this->subtotal,
            'total' => $this->total,
        ];

        $photoPaths = [];
        foreach ($this->photos as $photo) {
            $photoPaths[] = $photo->store('budget-documents', 'public');
        }
        if ($photoPaths) {
            $content['photos'] = $photoPaths;
        }

        $budget = $this->createBudget($content);

        $this->sendNotifications($budget);

        $this->code = $budget->code;
        $this->isSubmitted = true;
    }

    public function resetForm()
    {
        $this->isSubmitted = false;
        $this->currentStep = 1;
        $this->items = [];
        $this->addItem();
        $this->customer_name = '';
        $this->customer_phone = '';
        $this->customer_email = '';
        $this->postcode = '';
        $this->street = '';
        $this->number = '';
        $this->complement = '';
        $this->neighborhood = '';
        $this->city = '';
        $this->state = '';
        $this->delivery_date = '';
        $this->delivery_period = '';
        $this->needs_pump = '';
        $this->notes = '';
        $this->shipping = 0;
        $this->photos = [];
        $this->turnstileToken = null;
        $this->recalculateAll();
        $this->code = '';
    }

    public function render(TurnstileService $turnstile)
    {
        return view('livewire.budget', [
            'turnstileEnabled' => $turnstile->isEnabled(),
            'turnstileSiteKey' => $turnstile->getSiteKey(),
            'allProducts' => $this->getAllProductsPlucked(),
            'deliveryPeriods' => self::DELIVERY_PERIODS,
            'pumpOptions' => self::PUMP_OPTIONS,
        ]);
    }

    /**
     * Formata o telefone no padrão (99) 99999-9999 ou (99) 9999-9999.
     */
    public static function formatPhone(string $phone): string
    {
        $digits = preg_replace('/\D/', '', $phone);

        return match (strlen($digits)) {
            11 => sprintf('(%s) %s-%s', substr($digits, 0, 2), substr($digits, 2, 5), substr($digits, 7)),
            10 => sprintf('(%s) %s-%s', substr($digits, 0, 2), substr($digits, 2, 4), substr($digits, 6)),
            default => trim($phone),
        };
    }

    protected function itemRules(): array
    {
        return [
            'items' => 'required|array|min:1|max:20',
            'items.*.product_id' => [
                'required',
                Rule::exists('products', 'id')->where('is_active', true)->whereNull('deleted_at'),
            ],
            'items.*.option_id' => [
                'required',
                function (string $attribute, mixed $value, \Closure $fail) {
                    $index = explode('.', $attribute)[1];
                    $productId = $this->items[$index]['product_id'] ?? null;

                    $belongsToProduct = ProductOption::query()
                        ->whereKey($value)
                        ->where('product_id', $productId)
                        ->where('is_active', true)
                        ->exists();

                    if (! $belongsToProduct) {
                        $fail('Selecione uma opção válida para este produto.');
                    }
                },
            ],
            'items.*.quantity' => [
                'required',
                'numeric',
                'min:0.5',
                'max:10000',
                function (string $attribute, mixed $value, \Closure $fail) {
                    $index = explode('.', $attribute)[1];
                    $min = (float) ($this->getCachedProduct($this->items[$index]['product_id'] ?? null)?->min_quantity ?? 1);

                    if ((float) $value < $min) {
                        $fail('A quantidade mínima para este produto é '.BudgetCalculatorService::formatQuantity($min).'.');
                    }
                },
            ],
            'photos' => 'array|max:4',
            'photos.*' => 'image|max:4096',
        ];
    }

    protected function itemMessages(): array
    {
        return [
            'items.*.product_id.required' => 'Selecione o produto.',
            'items.*.product_id.exists' => 'Este produto não está mais disponível.',
            'items.*.option_id.required' => 'Selecione uma opção.',
            'items.*.quantity.required' => 'Informe a quantidade.',
            'items.*.quantity.numeric' => 'A quantidade deve ser um número.',
            'items.*.quantity.min' => 'A quantidade mínima é 0,5.',
            'items.*.quantity.max' => 'Quantidade muito alta. Fale com a nossa equipe.',
            'photos.max' => 'Você pode enviar no máximo 4 fotos.',
            'photos.*.image' => 'Os arquivos devem ser imagens.',
            'photos.*.max' => 'Cada foto deve ter no máximo 4MB.',
        ];
    }

    protected function contactRules(): array
    {
        return [
            'customer_name' => 'required|string|min:2|max:255',
            'customer_phone' => [
                'required',
                function (string $attribute, mixed $value, \Closure $fail) {
                    $digits = strlen(preg_replace('/\D/', '', (string) $value));
                    if ($digits < 10 || $digits > 11) {
                        $fail('Informe um telefone válido com DDD.');
                    }
                },
            ],
            'customer_email' => 'required|email|max:255',
            'postcode' => ['required', 'string', 'size:9', new CepInOperationAreaRule],
            'number' => 'required|string|max:20',
            'complement' => 'nullable|string|max:255',
            'delivery_date' => 'nullable|date|after_or_equal:today',
            'delivery_period' => ['nullable', Rule::in(array_keys(self::DELIVERY_PERIODS))],
            'needs_pump' => ['nullable', Rule::in(array_keys(self::PUMP_OPTIONS))],
            'notes' => 'nullable|string|max:1000',
        ];
    }

    protected function contactMessages(): array
    {
        return [
            'customer_name.required' => 'Informe seu nome.',
            'customer_phone.required' => 'Informe seu WhatsApp.',
            'customer_email.required' => 'Informe seu e-mail.',
            'customer_email.email' => 'Informe um e-mail válido.',
            'postcode.required' => 'Informe o CEP da obra.',
            'postcode.size' => 'Informe o CEP completo (00000-000).',
            'number.required' => 'Informe o número (ou S/N).',
            'delivery_date.after_or_equal' => 'A data de entrega não pode estar no passado.',
            'notes.max' => 'As observações devem ter no máximo 1000 caracteres.',
        ];
    }

    /**
     * Busca o preço e a unidade de cada item diretamente no banco (sem cache).
     */
    protected function refreshPricesFromDatabase(): void
    {
        $options = ProductOption::whereIn('id', collect($this->items)->pluck('option_id')->filter())
            ->get()
            ->keyBy('id');

        foreach ($this->items as $index => $item) {
            $option = $options->get($item['option_id']);
            $price = (float) ($option?->price ?? 0);
            $quantity = (float) $item['quantity'];

            $this->items[$index]['quantity'] = $quantity;
            $this->items[$index]['price'] = $price;
            $this->items[$index]['unit'] = $option?->unit?->value ?? '';
            $this->items[$index]['subtotal'] = BudgetCalculatorService::calculateItemSubtotal($quantity, $price);
        }
    }

    protected function formattedProducts(): array
    {
        return collect($this->items)->map(fn ($i) => [
            'product' => $i['product_id'],
            'product_option' => $i['option_id'],
            'quantity' => $i['quantity'],
            'price' => $i['price'],
            'subtotal' => $i['subtotal'],
        ])->toArray();
    }

    protected function shippingForPostcode(string $postcode): float
    {
        return (float) (OperationAreaService::resultForCep($postcode)['shipping_fee'] ?? 0);
    }

    /**
     * Cria o orçamento, os itens e os anexos numa transação.
     * Se dois pedidos chegarem ao mesmo tempo com o mesmo código, gera outro e tenta de novo.
     */
    protected function createBudget(array $content): BudgetModel
    {
        $attempts = 0;

        while (true) {
            try {
                return DB::transaction(function () use ($content) {
                    $budget = BudgetModel::create([
                        'user_id' => null,
                        'content' => $content,
                    ]);

                    foreach ($this->items as $item) {
                        $budget->budgetItems()->create([
                            'product_id' => $item['product_id'],
                            'product_option_id' => $item['option_id'],
                            'location_id' => null,
                            'quantity' => $item['quantity'],
                            'price' => $item['price'],
                            'subtotal' => $item['subtotal'],
                        ]);
                    }

                    foreach ($content['photos'] ?? [] as $index => $photoPath) {
                        $fullPath = storage_path('app/public/'.$photoPath);
                        if (file_exists($fullPath)) {
                            $budget->documents()->create([
                                'title' => 'Foto do Local (Anexo '.($index + 1).')',
                                'description' => 'Foto recebida via formulário.',
                                'file_path' => $photoPath,
                                'file_name' => basename($photoPath),
                                'file_size' => filesize($fullPath),
                                'file_type' => mime_content_type($fullPath),
                            ]);
                        }
                    }

                    return $budget;
                });
            } catch (UniqueConstraintViolationException $e) {
                if (++$attempts >= 3) {
                    throw $e;
                }
            }
        }
    }

    /**
     * Falhas de e-mail não podem impedir o pedido: ele já está salvo.
     */
    protected function sendNotifications(BudgetModel $budget): void
    {
        try {
            Mail::to(User::first()?->email ?? config('mail.from.address'))
                ->send(new AdminNewBudgetMail($budget));
        } catch (\Throwable $e) {
            report($e);
        }

        try {
            Mail::to($budget->content['customer_email'])
                ->send(new CustomerBudgetReceivedMail($budget));
        } catch (\Throwable $e) {
            report($e);
        }
    }

    protected function getCachedProduct($id)
    {
        if (! $id) {
            return null;
        }
        if (! array_key_exists($id, $this->productsCache)) {
            $this->productsCache[$id] = Cache::remember(Product::cacheKey($id), 86400, fn () => Product::find($id));
        }

        return $this->productsCache[$id];
    }

    protected function getCachedProductOption($id)
    {
        if (! $id) {
            return null;
        }
        if (! array_key_exists($id, $this->productOptionsCache)) {
            $this->productOptionsCache[$id] = Cache::remember(ProductOption::cacheKey($id), 86400, fn () => ProductOption::find($id));
        }

        return $this->productOptionsCache[$id];
    }

    protected function getAllProductsPlucked()
    {
        return Cache::remember(Product::CATALOG_CACHE_KEY, 86400, fn () => Product::where('is_active', true)
            ->whereHas('productOption', fn ($q) => $q->where('is_active', true))
            ->orderBy('name')
            ->pluck('name', 'id')
            ->toArray());
    }

    public function getOptionsForProduct($productId)
    {
        if (! $productId) {
            return [];
        }

        return Cache::remember(ProductOption::catalogCacheKey($productId), 86400, fn () => ProductOption::where('product_id', $productId)
            ->where('is_active', true)
            ->orderBy('name')
            ->pluck('name', 'id')
            ->toArray());
    }
}
