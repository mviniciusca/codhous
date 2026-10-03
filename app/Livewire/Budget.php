<?php

namespace App\Livewire;

use App\Models\Budget as BudgetModel;
use App\Models\Product;
use App\Models\ProductOption;
use App\Models\User;
use App\Services\BudgetCalculatorService;
use App\Services\OperationAreaService;
use App\Services\PostcodeFinderService;
use App\Services\TurnstileService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Livewire\Component;
use Livewire\WithFileUploads;

class Budget extends Component
{
    use WithFileUploads;

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
    public string $neighborhood = '';
    public string $city = '';
    public string $state = '';
    public float $shipping = 0;
    
    // Derived
    public float $subtotal = 0;
    public float $total = 0;
    public float $totalQuantity = 0;
    public string $code = '';

    // Caches
    protected static array $productsCache = [];
    protected static array $productOptionsCache = [];

    public function mount()
    {
        $this->code = BudgetModel::generateUniqueCode();
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

            if ($field === 'product_id') {
                $this->items[$index]['option_id'] = '';
                $this->items[$index]['price'] = 0;
                $this->items[$index]['subtotal'] = 0;
                
                $productId = $this->items[$index]['product_id'];
                if ($productId) {
                    $product = $this->getCachedProduct($productId);
                    $min = $product?->min_quantity ?? 1;
                    $this->items[$index]['min_quantity'] = $min;
                    $this->items[$index]['quantity'] = $min;
                }
            }

            if ($field === 'option_id') {
                $optionId = $this->items[$index]['option_id'];
                if ($optionId) {
                    $option = $this->getCachedProductOption($optionId);
                    $this->items[$index]['price'] = $option?->price ?? 0;
                    $this->items[$index]['unit'] = $option?->unit?->value ?? '';
                } else {
                    $this->items[$index]['price'] = 0;
                    $this->items[$index]['unit'] = '';
                }
            }
            
            if ($field === 'quantity') {
                $min = $this->items[$index]['min_quantity'] ?? 1;
                $qty = (float) ($this->items[$index]['quantity'] ?: 0);
                if ($qty < $min) {
                    $this->items[$index]['quantity'] = $min;
                    $qty = $min;
                }
            }

            $qty = (float) ($this->items[$index]['quantity'] ?? 0);
            $price = (float) ($this->items[$index]['price'] ?? 0);
            $this->items[$index]['subtotal'] = BudgetCalculatorService::calculateItemSubtotal($qty, $price);

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
            $postcodeService = new PostcodeFinderService($value, function($key, $val) {
                $k = str_replace('content.', '', $key);
                if (property_exists($this, $k)) {
                    $this->$k = $val ?? '';
                }
            });
            $postcodeService->find();
            
            $result = OperationAreaService::resultForCep($value);
            $this->shipping = $result['shipping_fee'] ?? 0;
            $this->recalculateAll();
        }
    }

    public function recalculateAll()
    {
        $formattedProducts = collect($this->items)->map(function($i) {
            return [
                'product' => $i['product_id'],
                'product_option' => $i['option_id'],
                'quantity' => $i['quantity'],
                'price' => $i['price'],
                'subtotal' => $i['subtotal']
            ];
        })->toArray();

        $result = BudgetCalculatorService::calculateTotal($formattedProducts, $this->shipping, 0, 0);
        $this->totalQuantity = $result['quantity'];
        $this->subtotal = $result['subtotal'];
        $this->total = $result['total'];
    }

    public function setStep($step)
    {
        if ($step == 2) {
            $this->validate([
                'items.*.product_id' => 'required',
                'items.*.option_id' => 'required',
                'items.*.quantity' => 'required|numeric|min:1',
                'photos' => 'array|max:4',
                'photos.*' => 'image|max:4096',
            ], [
                'items.*.product_id.required' => 'Selecione o produto.',
                'items.*.option_id.required' => 'Selecione uma opção.',
                'photos.max' => 'Você pode enviar no máximo 4 fotos.',
                'photos.*.image' => 'Os arquivos devem ser imagens.',
                'photos.*.max' => 'Cada foto deve ter no máximo 4MB.',
            ]);
        }
        
        if ($step == 3) {
            if ($this->currentStep < 3) {
                $this->validate([
                    'customer_name' => 'required',
                    'customer_phone' => 'required',
                    'customer_email' => 'required|email',
                    'postcode' => ['required', 'string', 'size:9', new \App\Rules\CepInOperationAreaRule],
                    'number' => 'required'
                ]);
            }
        }

        $this->currentStep = $step;
    }

    public function submit(TurnstileService $turnstile)
    {
        $this->validate([
            'customer_name' => 'required',
            'customer_phone' => 'required',
            'customer_email' => 'required|email',
            'postcode' => ['required', 'string', 'size:9', new \App\Rules\CepInOperationAreaRule],
            'number' => 'required'
        ]);

        if ($turnstile->isEnabled() && !$turnstile->verify($this->turnstileToken, request()->ip())) {
            $this->addError('turnstileToken', 'A verificação anti-spam falhou. Atualize a página.');
            return;
        }

        $content = [
            'products' => collect($this->items)->map(fn($i) => [
                'product' => $i['product_id'],
                'product_option' => $i['option_id'],
                'quantity' => $i['quantity'],
                'price' => $i['price'],
                'subtotal' => $i['subtotal']
            ])->toArray(),
            'customer_name' => $this->customer_name,
            'customer_phone' => $this->customer_phone,
            'customer_email' => $this->customer_email,
            'postcode' => $this->postcode,
            'street' => $this->street,
            'number' => $this->number,
            'neighborhood' => $this->neighborhood,
            'city' => $this->city,
            'state' => $this->state,
            'shipping' => $this->shipping,
            'quantity' => $this->totalQuantity,
            'price' => $this->items[0]['price'] ?? 0,
            'subtotal' => $this->subtotal,
            'total' => $this->total,
        ];

        if (!empty($this->photos)) {
            $content['photos'] = [];
            foreach ($this->photos as $photo) {
                $content['photos'][] = $photo->store('budget-documents', 'public');
            }
        }

        $budget = BudgetModel::create([
            'code' => $this->code,
            'content' => $content,
        ]);

        foreach ($this->items as $req) {
            if ($req['product_id']) {
                $budget->budgetItems()->create([
                    'product_id' => $req['product_id'],
                    'product_option_id' => $req['option_id'],
                    'location_id' => null,
                    'quantity' => $req['quantity'] ?? 1,
                    'price' => $req['price'] ?? 0,
                    'subtotal' => $req['subtotal'] ?? 0,
                ]);
            }
        }

        if (!empty($content['photos'])) {
            foreach ($content['photos'] as $index => $photoPath) {
                $fullPath = storage_path('app/public/' . $photoPath);
                if (file_exists($fullPath)) {
                    $budget->documents()->create([
                        'title' => 'Foto do Local (Anexo ' . ($index + 1) . ')',
                        'description' => 'Foto recebida via formulário.',
                        'file_path' => $photoPath,
                        'file_name' => basename($photoPath),
                        'file_size' => filesize($fullPath),
                        'file_type' => mime_content_type($fullPath),
                    ]);
                }
            }
        }

        Mail::to(User::first()?->email ?? config('mail.from.address'))
            ->send(new \App\Mail\AdminNewBudgetMail($budget));
        
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
        $this->neighborhood = '';
        $this->city = '';
        $this->state = '';
        $this->shipping = 0;
        $this->photos = [];
        $this->recalculateAll();
        $this->code = BudgetModel::generateUniqueCode();
    }

    public function render(TurnstileService $turnstile)
    {
        return view('livewire.budget', [
            'turnstileEnabled' => $turnstile->isEnabled(),
            'turnstileSiteKey' => $turnstile->getSiteKey(),
            'allProducts' => $this->getAllProductsPlucked(),
        ]);
    }

    protected function getCachedProduct($id)
    {
        if (!$id) return null;
        if (!array_key_exists($id, self::$productsCache)) {
            self::$productsCache[$id] = Cache::remember('product_' . $id, 86400, fn () => Product::find($id));
        }
        return self::$productsCache[$id];
    }

    protected function getCachedProductOption($id)
    {
        if (!$id) return null;
        if (!array_key_exists($id, self::$productOptionsCache)) {
            self::$productOptionsCache[$id] = Cache::remember('product_option_' . $id, 86400, fn () => ProductOption::find($id));
        }
        return self::$productOptionsCache[$id];
    }

    protected function getAllProductsPlucked()
    {
        return Cache::remember('all_products_plucked', 86400, fn () => Product::pluck('name', 'id')->toArray());
    }

    public function getOptionsForProduct($productId)
    {
        if (!$productId) return [];
        return Cache::remember('product_options_plucked_' . $productId, 86400, fn () => ProductOption::where('product_id', $productId)->pluck('name', 'id')->toArray());
    }
}
