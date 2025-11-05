<?php

namespace App\Filament\Resources\BudgetResource\Pages;

use App\Filament\Resources\BudgetResource;
use App\Mail\BudgetMail;
use App\Models\Budget;
use App\Models\BudgetHistory;
use App\Models\Location;
use App\Models\Product;
use App\Models\ProductOption;
use App\Services\BudgetPdfService;
use App\Services\FakeBudgetDataService;
use App\Services\PdfGenerator;
use App\Services\PostcodeFinder;
use App\Services\SendBudgetMail;
use App\Trait\BudgetStatus;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;  // For form actions
use Filament\Forms\Components\Actions\Action as FormAction; // For component actions
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\View;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class EditBudget extends EditRecord
{
    use BudgetStatus;

    protected static string $resource = BudgetResource::class;

    public function getTitle(): string|Htmlable
    {
        return __('Budget Details');
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Group::make()
                    ->columnSpanFull()
                    ->schema([
                        Section::make(__('Budget Overview'))
                            ->headerActions([
                                FormAction::make('notify_email')
                                    ->icon('heroicon-o-envelope')
                                    ->label(__('Send'))
                                    ->disabled(function (Get $get, ?array $state) {
                                        return self::checkId($get, $state);
                                    })
                                    ->color(function (Get $get, ?array $state) {
                                        if (self::checkId($get, $state)) {
                                            return 'gray';
                                        } else {
                                            return 'primary';
                                        }
                                    })
                                    ->outlined()
                                    ->requiresConfirmation()
                                    ->action(function (Get $get, ?array $state) {
                                        $mail = new SendBudgetMail(
                                            $state,
                                            $get('content.customer_email'),
                                            new BudgetMail($state)
                                        );
                                        $mail->dispatch();
                                    }),
                                FormAction::make('download_pdf')
                                    ->label(__('Save'))
                                    ->icon('heroicon-o-document-arrow-down')
                                    ->color('warning')
                                    ->outlined()
                                    ->requiresConfirmation()
                                    ->modalHeading(__('Download PDF'))
                                    ->modalDescription(__('Are you sure you want to download this budget as PDF?'))
                                    ->modalSubmitActionLabel(__('Yes, download'))
                                    ->action(function () {
                                        $budget = Budget::findOrFail($this->record->id);
                                        $pdfService = new BudgetPdfService();
                                        $pdfModel = $pdfService->generatePdf($budget, true);

                                        if ($pdfModel && $pdfModel->fileExists()) {
                                            return response()->download(
                                                $pdfModel->getFullPath(),
                                                'Budget_'.$budget->code.'.pdf',
                                                ['Content-Type' => 'application/pdf']
                                            );
                                        } else {
                                            Notification::make()
                                                ->title(__('Error generating PDF'))
                                                ->body(__('Could not generate PDF file. Please try again.'))
                                                ->danger()
                                                ->send();
                                        }
                                    }),
                                FormAction::make('generate_link')
                                    ->label(__('Generate PDF Link'))
                                    ->icon('heroicon-o-link')
                                    ->color('primary')
                                    ->outlined()
                                    ->action(function (Get $get, Set $set) {
                                        $budget = Budget::findOrFail($this->record->id);
                                        $budget->refresh();

                                        $pdfService = new BudgetPdfService();
                                        $pdfModel = $pdfService->generatePdf($budget, true);

                                        if ($pdfModel) {
                                            $url = $pdfModel->getDownloadUrl();

                                            // Store the URL in the share_link field
                                            $set('content.share_link', $url);

                                            // Save the budget to persist the share link
                                            $content = $budget->content;
                                            $content['share_link'] = $url;
                                            $budget->update(['content' => $content]);

                                            Notification::make()
                                                ->title(__('PDF download link generated!'))
                                                ->success()
                                                ->send();
                                        } else {
                                            Notification::make()
                                                ->title(__('Error generating PDF link'))
                                                ->body(__('Could not generate PDF download link. Please check logs.'))
                                                ->danger()
                                                ->send();
                                        }
                                    }),
                                FormAction::make('share_whatsapp')
                                    ->label(__('WhatsApp'))
                                    ->icon('heroicon-o-phone')
                                    ->outlined()
                                    ->color('success')
                                    ->visible(function () {
                                        $budget = Budget::with(['pdfs'])->findOrFail($this->record->id);

                                        return ! empty($budget->content['share_link'] ?? null);
                                    })
                                    ->action(function () {
                                        $budget = Budget::findOrFail($this->record->id);
                                        $whatsApp = new \App\Services\WhatsAppShare();
                                        $message = __("Hello! Here's your budget link: ").($budget->content['share_link'] ?? '');
                                        $url = $whatsApp->generateUrl(
                                            $budget->content['customer_phone'] ?? '',
                                            $message
                                        );

                                        return redirect()->away($url);
                                    }),
                            ])
                            ->columns(4)
                            ->description(__('Organize your budget report'))
                            ->icon('heroicon-o-document')
                            ->schema([
                                Toggle::make('is_active')
                                    ->helperText(__('Enable or disable this budget from the dashboard view'))
                                    ->label(__('Active'))
                                    ->default(true)
                                    ->inline(),
                                Select::make('status')
                                    ->helperText(__('Set the budget status'))
                                    ->prefixIcon('heroicon-o-tag')
                                    ->options([
                                        'pending'  => __('Pending'),
                                        'on going' => __('On Going'),
                                        'done'     => __('Done'),
                                        'ignored'  => __('Ignored'),
                                    ])
                                    ->default('pending'),
                                TextInput::make('code')
                                    ->disabled()
                                    ->dehydrated()
                                    ->prefix('#')
                                    ->label(__('Budget Code'))
                                    ->helperText(__('Use this code to search'))
                                    ->default('ADMIN'.rand(10000, 99999)),
                                DateTimePicker::make('created_at')
                                    ->disabled()
                                    ->dehydrated()
                                    ->format('Y-m-d H:i:s')
                                    ->prefixIcon('heroicon-o-calendar')
                                    ->displayFormat('d/m/Y H:i')
                                    ->default(fn () => Carbon::now()->format('Y-m-d H:i:s'))
                                    ->label(__('Date'))
                                    ->helperText(__('When this budget was created')),
                            ]),
                    ]),
                \Filament\Forms\Components\Tabs::make('budget_tabs')
                    ->tabs([
                        \Filament\Forms\Components\Tabs\Tab::make('customer_information')
                            ->label(__('Customer Information'))
                            ->icon('heroicon-o-user')
                            ->schema([
                                Group::make()
                                    ->columns(3)
                                    ->columnSpanFull()
                                    ->schema([
                                        TextInput::make('content.customer_name')
                                            ->dehydrated()
                                            ->required()
                                            ->prefixIcon('heroicon-o-user')
                                            ->helperText(__('Customer name'))
                                            ->default('Admin')
                                            ->label(__('Customer Name')),
                                        TextInput::make('content.customer_email')
                                            ->dehydrated()
                                            ->email()
                                            ->required()
                                            ->prefixIcon('heroicon-o-envelope')
                                            ->helperText(__('Customer email address'))
                                            ->label(__('Email')),
                                        TextInput::make('content.customer_phone')
                                            ->required()
                                            ->helperText(__('Phone Number'))
                                            ->prefixIcon('heroicon-o-phone')
                                            ->tel()
                                            ->mask('(99)99999-9999')
                                            ->placeholder(_('(--) ---- ----'))
                                            ->helperText(__('Customer phone number'))
                                            ->label(__('Phone')),
                                    ]),
                                Group::make()
                                    ->columns(3)
                                    ->columnSpanFull()
                                    ->schema([
                                        TextInput::make('content.postcode')
                                            ->required()
                                            ->minLength(9)
                                            ->mask('99999-999')
                                            ->prefixIcon('heroicon-o-map-pin')
                                            ->placeholder('----- ---')
                                            ->helperText(__('Customer postcode'))
                                            ->maxLength(9)
                                            ->suffixAction(
                                                fn ($state, Set $set, $livewire) => FormAction::make('search-action')
                                                    ->icon('heroicon-o-magnifying-glass')
                                                    ->action(function () use ($state, $livewire, $set) {
                                                        $livewire->validateOnly('data.content.postcode');
                                                        $postcode = new PostcodeFinder($state, $set);
                                                        $postcode->find();
                                                    })
                                            )
                                            ->label(__('CEP')),
                                        TextInput::make('content.street')
                                            ->disabled()
                                            ->dehydrated()
                                            ->required()
                                            ->prefixIcon('heroicon-o-map-pin')
                                            ->helperText(__('Customer street.'))
                                            ->label(__('Street')),
                                        TextInput::make('content.number')
                                            ->dehydrated()
                                            ->prefixIcon('heroicon-o-map-pin')
                                            ->helperText(__('Customer street number. Optional'))
                                            ->label(__('Number')),
                                        TextInput::make('content.city')
                                            ->disabled()
                                            ->dehydrated()
                                            ->prefixIcon('heroicon-o-map-pin')
                                            ->helperText(__('Customer city.'))
                                            ->label(__('City')),
                                        TextInput::make('content.neighborhood')
                                            ->disabled()
                                            ->dehydrated()
                                            ->prefixIcon('heroicon-o-map-pin')
                                            ->helperText(__('Customer neighborhood.'))
                                            ->label(__('Neighborhood')),
                                        TextInput::make('content.state')
                                            ->disabled()
                                            ->dehydrated()
                                            ->prefixIcon('heroicon-o-map-pin')
                                            ->helperText(__('Customer UF.'))
                                            ->label(__('State')),
                                    ]),
                            ]),

                        \Filament\Forms\Components\Tabs\Tab::make('budget_content')
                            ->label(__('Budget Content'))
                            ->icon('heroicon-o-shopping-bag')
                            ->schema([
                                \Filament\Forms\Components\Repeater::make('content.products')
                                    ->label(__('Product List'))
                                    ->collapsed()
                                    ->live()
                                    ->afterStateUpdated(function (Get $get, Set $set) {
                                        // Recalcular totais quando produtos são adicionados ou removidos
                                        self::calculateTotal($get, $set);
                                    })
                                    ->schema([
                                        Group::make()
                                            ->columnSpanFull()
                                            ->columns(6)
                                            ->schema([
                                                Select::make('product')
                                                    ->live()
                                                    ->dehydrated()
                                                    ->required()
                                                    ->columnSpan(3)
                                                    ->searchable()
                                                    ->prefixIcon('heroicon-o-shopping-cart')
                                                    ->label(__('Product'))
                                                    ->helperText(__('Product selected'))
                                                    ->options(Product::all()->pluck('name', 'id'))
                                                    ->afterStateUpdated(function (Get $get, Set $set) {
                                                        $set('product_option', null);
                                                        $set('price', null);
                                                    }),
                                                Select::make('product_option')
                                                    ->live()
                                                    ->dehydrated()
                                                    ->searchable()
                                                    ->prefixIcon('heroicon-o-shopping-bag')
                                                    ->columnSpan(3)
                                                    ->label(__('Option'))
                                                    ->helperText(__('Option selected'))
                                                    ->options(fn (Get $get): Collection => self::getOptions($get))
                                                    ->required(fn (Get $get): bool => self::getOptions($get)->count() > 0)
                                                    ->hidden(fn (Get $get): bool => self::getOptions($get)->count() == 0)
                                                    ->afterStateUpdated(fn (Get $get, Set $set, $state) => self::updatePrice($get, $set, $state))
                                                    ->afterStateHydrated(function (Get $get, Set $set, $state) {
                                                        // Garantir que o price seja carregado corretamente
                                                        if ($state && $get('product')) {
                                                            self::updatePrice($get, $set, $state);
                                                        }
                                                    }),
                                            ]),
                                        Group::make()
                                            ->columnSpanFull()
                                            ->columns(6)
                                            ->schema([
                                                Select::make('location')
                                                    ->dehydrated()
                                                    ->required()
                                                    ->columnSpan(3)
                                                    ->label(__('Local / Area'))
                                                    ->helperText(__('Local or area to be concreted'))
                                                    ->searchable()
                                                    ->prefixIcon('heroicon-o-map-pin')
                                                    ->options(Location::all()
                                                        ->pluck('name', 'id')),
                                                TextInput::make('quantity')
                                                    ->live(onBlur: true)
                                                    ->integer()
                                                    ->step(1)
                                                    ->required()
                                                    ->minValue(3)
                                                    ->columnSpan(3)
                                                    ->label(__('Quantity'))
                                                    ->suffix(__('m³'))
                                                    ->helperText(__('Min value is 3 (ABNT NBR 7212)'))
                                                    ->afterStateHydrated(function (Get $get, Set $set, $state) {
                                                        // Garantir que seja inteiro ao carregar
                                                        if ($state !== null) {
                                                            $set('quantity', (int) $state);
                                                        }
                                                    })
                                                    ->afterStateUpdated(function (Get $get, Set $set, $state) {
                                                        // Garantir que seja inteiro
                                                        $set('quantity', (int) $state);

                                                        // Calcular subtotal do item atual
                                                        self::calculateItemSubtotal($get, $set);

                                                        // Recalcular total geral e atualizar quantidade total
                                                        self::calculateTotal($get, $set);
                                                    }),
                                            ]),
                                        Group::make()
                                            ->columnSpanFull()
                                            ->columns(6)
                                            ->schema([
                                                TextInput::make('price')
                                                    ->live(onBlur: true)
                                                    ->disabled()
                                                    ->dehydrated()
                                                    ->columnSpan(3)
                                                    ->helperText(__('Price of product in '.env('CURRENCY_SUFFIX')))
                                                    ->afterStateHydrated(function (Get $get, Set $set) {
                                                        self::getPrice($get, $set);
                                                    })
                                                    ->prefix(env('CURRENCY_SUFFIX'))
                                                    ->label(__('Price per Unity'))
                                                    ->required(),
                                                TextInput::make('subtotal')
                                                    ->live()
                                                    ->disabled()
                                                    ->dehydrated()
                                                    ->columnSpan(3)
                                                    ->prefix(env('CURRENCY_SUFFIX'))
                                                    ->label(__('Subtotal'))
                                                    ->helperText(__('Product quantity x price'))
                                                    ->afterStateHydrated(fn (Get $get, Set $set) => self::calculateItemSubtotal($get, $set)),
                                            ]),
                                    ])
                                    ->columns(1)
                                    ->itemLabel(fn (array $state): ?string => $state['product'] ? Product::find($state['product'])?->name.' ('.($state['quantity'] ?? 0).' m³)' : null)
                                    ->addActionLabel(__('Add Product'))
                                    ->collapsible()
                                    ->afterStateUpdated(function (Get $get, Set $set) {
                                        self::calculateTotal($get, $set);
                                    })
                                    ->afterStateHydrated(function (Get $get, Set $set, ?array $state) {
                                        // Garantir que o repeater tem os dados quando carregado
                                        if ($get('id')) {
                                            // Buscar o orçamento completo com todos os dados
                                            $budget = Budget::findOrFail($get('id'));
                                            if (isset($budget->content['products']) && ! empty($budget->content['products'])) {
                                                // Garantir que estamos definindo os produtos independentemente do estado atual
                                                $set('content.products', $budget->content['products']);
                                                self::calculateTotal($get, $set);
                                            }
                                        }
                                    })
                                    ->reorderable()
                                    ->defaultItems(1)
                                    ->columnSpanFull(),
                                Section::make(__('Pricing Summary'))
                                    ->description(__('Pricing Definition & Total Cost'))
                                    ->icon('heroicon-o-currency-dollar')
                                    ->collapsible()
                                    ->schema([
                                        Group::make()
                                            ->columns(5)
                                            ->columnSpanFull()
                                            ->schema([
                                                TextInput::make('content.quantity')
                                                    ->live(onBlur: true)
                                                    ->disabled()
                                                    ->dehydrated()
                                                    ->readonly()
                                                    ->required()
                                                    ->integer()
                                                    ->step(1)
                                                    ->suffix('m³')
                                                    ->label(__('Total Quantity'))
                                                    ->helperText(__('Total quantity for all products'))
                                                    ->afterStateHydrated(function (Get $get, Set $set, ?string $state) {
                                                        self::calculateTotal($get, $set);
                                                    }),
                                                TextInput::make('content.shipping')
                                                    ->live(onBlur: true)
                                                    ->dehydrated()
                                                    ->numeric()
                                                    ->required()
                                                    ->default(0)
                                                    ->prefix(env('CURRENCY_SUFFIX'))
                                                    ->helperText(__('Total shipping cost in '.env('CURRENCY_SUFFIX')))
                                                    ->step(0.01)
                                                    ->afterStateUpdated(function (Get $get, Set $set, ?string $state) {
                                                        self::calculateTotal($get, $set);
                                                    }),
                                                TextInput::make('content.tax')
                                                    ->live(onBlur: true)
                                                    ->dehydrated()
                                                    ->prefix('+'.env('CURRENCY_SUFFIX'))
                                                    ->numeric()
                                                    ->required()
                                                    ->default(0)
                                                    ->helperText(__('Sum tax or other values in '.env('CURRENCY_SUFFIX')))
                                                    ->step(0.01)
                                                    ->afterStateUpdated(function (Get $get, Set $set, ?string $state) {
                                                        self::calculateTotal($get, $set);
                                                    }),
                                                TextInput::make('content.discount')
                                                    ->live(onBlur: true)
                                                    ->dehydrated()
                                                    ->numeric()
                                                    ->required()
                                                    ->default(0)
                                                    ->prefix('-'.env('CURRENCY_SUFFIX'))
                                                    ->helperText(__('Applies a discount in '.env('CURRENCY_SUFFIX')))
                                                    ->step(0.01)
                                                    ->afterStateUpdated(function (Get $get, Set $set, ?string $state) {
                                                        self::calculateTotal($get, $set);
                                                    }),
                                                TextInput::make('content.total')
                                                    ->disabled()
                                                    ->dehydrated()
                                                    ->required()
                                                    ->prefix(env('CURRENCY_SUFFIX'))
                                                    ->label(__('Total Price'))
                                                    ->helperText(__('The total budget value in '.env('CURRENCY_SUFFIX')))
                                                    ->suffixAction(function () {
                                                        return FormAction::make('calculator')
                                                            ->icon('heroicon-o-calculator')
                                                            ->color('gray')
                                                            ->disabled()
                                                            ->visible(true)
                                                            ->action(fn () => self::generateCode($this->data));
                                                    }),
                                            ]),
                                    ]),
                            ]),

                        \Filament\Forms\Components\Tabs\Tab::make('documents')
                            ->label(__('Documents'))
                            ->icon('heroicon-o-document-text')
                            ->schema([
                                Section::make(__('Share Links'))
                                    ->description(__('Generated links for this budget'))
                                    ->headerActions([
                                        FormAction::make('notify_email')
                                            ->icon('heroicon-o-envelope')
                                            ->label(__('Send'))
                                            ->disabled(function (Get $get, ?array $state) {
                                                return self::checkId($get, $state);
                                            })
                                            ->color(function (Get $get, ?array $state) {
                                                if (self::checkId($get, $state)) {
                                                    return 'gray';
                                                } else {
                                                    return 'primary';
                                                }
                                            })
                                            ->outlined()
                                            ->requiresConfirmation()
                                            ->action(function (Get $get, ?array $state) {
                                                $mail = new SendBudgetMail(
                                                    $state,
                                                    $get('content.customer_email'),
                                                    new BudgetMail($state)
                                                );
                                                $mail->dispatch();
                                            }),
                                        FormAction::make('download_pdf')
                                            ->label(__('PDF'))
                                            ->icon('heroicon-o-document-arrow-down')
                                            ->color('warning')
                                            ->outlined()
                                            ->requiresConfirmation()
                                            ->modalHeading(__('Download PDF'))
                                            ->modalDescription(__('Are you sure you want to download this budget as PDF?'))
                                            ->modalSubmitActionLabel(__('Yes, download'))
                                            ->action(function () {
                                                $budget = Budget::findOrFail($this->record->id);
                                                $pdfService = new BudgetPdfService();
                                                $pdfModel = $pdfService->generatePdf($budget, true);

                                                if ($pdfModel && $pdfModel->fileExists()) {
                                                    return response()->download(
                                                        $pdfModel->getFullPath(),
                                                        'Budget_'.$budget->code.'.pdf',
                                                        ['Content-Type' => 'application/pdf']
                                                    );
                                                } else {
                                                    Notification::make()
                                                        ->title(__('Error generating PDF'))
                                                        ->body(__('Could not generate PDF file. Please try again.'))
                                                        ->danger()
                                                        ->send();
                                                }
                                            }),
                                    ])
                                    ->schema([
                                        TextInput::make('content.share_link')
                                            ->label(__('PDF Share Link'))
                                            ->helperText(__('Share this PDF link with customers'))
                                            ->placeholder(__('Generate a PDF link first'))
                                            ->disabled()
                                            ->dehydrated()
                                            ->suffixAction(
                                                FormAction::make('open_link')
                                                    ->icon('heroicon-m-arrow-top-right-on-square')
                                                    ->tooltip(__('View PDF'))
                                                    ->url(fn ($state) => $state)
                                                    ->openUrlInNewTab()
                                            )
                                            ->extraAttributes(['class' => 'flex-1'])
                                            ->suffixActions([
                                                FormAction::make('share_whatsapp')
                                                    ->icon('heroicon-m-chat-bubble-left-right')
                                                    ->tooltip(__('Share PDF'))
                                                    ->outlined()
                                                    ->action(function ($state, $record) {
                                                        $whatsApp = new \App\Services\WhatsAppShare();
                                                        $message = __("Hello! Here's your budget PDF link: ").$state;
                                                        $url = $whatsApp->generateUrl(
                                                            $record->content['customer_phone'] ?? '',
                                                            $message
                                                        );

                                                        return redirect()->away($url);
                                                    })
                                                    ->visible(fn ($state) => ! empty($state))
                                                    ->color('success'),
                                            ])
                                            ->columnSpanFull(),
                                    ]),
                            ]),

                        \Filament\Forms\Components\Tabs\Tab::make('resume')
                            ->label(__('Resume'))
                            ->icon('heroicon-o-document-text')
                            ->schema([
                                Section::make(__('Budget Summary'))
                                    ->schema([
                                        View::make('filament.forms.components.invoice'),
                                    ]),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }

    /**
     * Mount the component
     *
     * @return void
     */
    public function mount($record): void
    {
        parent::mount($record);

        // Carregar os produtos do relacionamento para o formulário
        $budget = Budget::with('products')->findOrFail($record);

        // Verificar se há produtos na tabela pivot
        if ($budget->products->count() > 0) {
            $productItems = [];

            foreach ($budget->products as $product) {
                // Buscar o nome da opção de produto para garantir que seja exibido corretamente
                $productOption = ProductOption::find($product->pivot->product_option_id);

                $productItems[] = [
                    'product'        => $product->id,
                    'product_option' => $product->pivot->product_option_id,
                    'location'       => $product->pivot->location_id,
                    'quantity'       => (int) $product->pivot->quantity,
                    'price'          => $product->pivot->price,
                    'subtotal'       => $product->pivot->subtotal,
                ];
            }

            // Definir produtos no campo content
            $this->data['content']['products'] = $productItems;

            // Recalcular totais
            $totalQuantity = $budget->products->sum('pivot.quantity');
            $subtotal = $budget->products->sum('pivot.subtotal');
            $shipping = $this->data['content']['shipping'] ?? 0;
            $tax = $this->data['content']['tax'] ?? 0;
            $discount = $this->data['content']['discount'] ?? 0;

            $this->data['content']['quantity'] = (int) $totalQuantity;
            $this->data['content']['total'] = number_format($subtotal + floatval($shipping) + floatval($tax) - floatval($discount), 2, '.', '');
        }
    }

    /**
     * Summary of save
     * @param bool $shouldRedirect
     * @param bool $shouldSendSavedNotification
     * @return void
     */
    public function save(bool $shouldRedirect = true, bool $shouldSendSavedNotification = true): void
    {
        // Armazenar os produtos para atualizar depois
        $products = $this->data['content']['products'] ?? [];

        // Chamar o método original de salvamento
        parent::save($shouldRedirect, $shouldSendSavedNotification);

        // Se temos produtos, atualizar o relacionamento
        if (! empty($products)) {
            $budget = Budget::findOrFail($this->record->id);

            // Limpar os produtos existentes
            $budget->products()->detach();

            // Adicionar os novos produtos
            foreach ($products as $product) {
                if (isset($product['product']) && isset($product['quantity'])) {
                    $budget->products()->attach($product['product'], [
                        'product_option_id' => $product['product_option'] ?? null,
                        'location_id'       => $product['location'] ?? null,
                        'quantity'          => (int) ($product['quantity'] ?? 0),
                        'price'             => $product['price'] ?? 0,
                        'subtotal'          => $product['subtotal'] ?? 0,
                    ]);
                }
            }

            // Atualizar os totais no campo content
            $totalQuantity = $budget->products()->sum('quantity');
            $subtotal = $budget->products()->sum('subtotal');

            $shipping = floatval($this->data['content']['shipping'] ?? 0);
            $tax = floatval($this->data['content']['tax'] ?? 0);
            $discount = floatval($this->data['content']['discount'] ?? 0);
            $total = $subtotal + $shipping + $tax - $discount;

            $content = $budget->content;
            $content['quantity'] = (int) $totalQuantity;
            $content['total'] = number_format($total, 2, '.', '');

            // Atualizar o orçamento com os novos valores
            $budget->update(['content' => $content]);
        }

        // Registrar na história do orçamento
        BudgetHistory::create([
            'budget_id' => $this->record->id,
            'user_id'   => Auth::user()->id,
            'action'    => 'update',
        ]);
    }

    protected function afterSave()
    {
        // Garantir que os produtos não sejam perdidos durante o salvamento
        if (! isset($this->data['content']['products']) || empty($this->data['content']['products'])) {
            // Se os produtos estiverem vazios após o salvamento, recuperá-los do banco de dados
            $budget = Budget::findOrFail($this->data['id']);

            if (isset($budget->content['products']) && ! empty($budget->content['products'])) {
                // Atualizar o orçamento com os produtos originais
                $this->data['content']['products'] = $budget->content['products'];

                // Salvar novamente para garantir que os produtos sejam mantidos
                Budget::findOrFail($this->data['id'])->update([
                    'content' => $this->data['content'],
                ]);
            }
        }
    }

    /**
     * Summary of calculateTotal
     * @param Get $get
     * @param Set $set
     * @return void
     */
    private static function calculateTotal(Get $get, Set $set): void
    {
        $products = $get('content.products') ?? [];
        $subtotal = 0;
        $quantity = 0;

        // Calcular subtotais de cada produto
        foreach ($products as $index => $product) {
            $productQuantity = intval($product['quantity'] ?? 0);
            $productPrice = floatval($product['price'] ?? 0);
            $itemSubtotal = $productQuantity * $productPrice;

            // Atualizar subtotal do item
            $set("content.products.{$index}.subtotal", number_format($itemSubtotal, 2, '.', ''));

            // Somar ao total geral
            $subtotal += $itemSubtotal;
            $quantity += $productQuantity;
        }

        // Atualizar quantidade total na calculadora de preço (como inteiro)
        $set('content.quantity', (int) $quantity);

        // Aplicar taxas, shipping e descontos
        $shipping = floatval($get('content.shipping') ?? 0);
        $tax = floatval($get('content.tax') ?? 0);
        $discount = floatval($get('content.discount') ?? 0);

        $total = $subtotal + $shipping + $tax - $discount;
        $set('content.total', number_format($total, 2, '.', ''));
    }

    /**
     * Summary de calculateItemSubtotal
     * @param Get $get
     * @param Set $set
     * @return void
     */
    private static function calculateItemSubtotal(Get $get, Set $set): void
    {
        $quantity = intval($get('quantity') ?? 0);
        $price = floatval($get('price') ?? 0);
        $subtotal = $quantity * $price;
        $set('subtotal', number_format($subtotal, 2, '.', ''));
    }

    /**
     * Summary of getOptions
     * @param Get $get
     * @return Collection
     */
    private static function getOptions(Get $get): Collection
    {
        $productId = $get('product');

        // Se não tiver um produto selecionado, retornar uma coleção vazia
        if (! $productId) {
            return collect();
        }

        // Buscar todas as opções do produto selecionado
        return ProductOption::where('product_id', '=', $productId)
            ->get()
            ->pluck('name', 'id');
    }

    /**
     * Summary of getPrice
     * @param Get $get
     * @param Set $set
     * @return void
     */
    private static function getPrice(Get $get, Set $set): void
    {
        $id = $get('product_option');
        $price = ProductOption::select(['price'])
            ->where('id', '=', $id)
            ->first();
        $set('price', $price->price ?? 0);
    }

    /**
     * Summary of updatePrice
     * @param Get $get
     * @param Set $set
     * @param mixed $productId
     * @return void
     */
    private static function updatePrice(Get $get, Set $set, $productId): void
    {
        if ($productId) {
            $price = ProductOption::where('id', $productId)
                ->value('price') ?? 0;
        } else {
            $price = 0;
        }
        $set('price', $price);
        self::calculateTotal($get, $set);
    }

    /**
     * Summary of checkId
     * @param Get $get
     * @param mixed $state
     * @return bool
     */
    private function checkId(Get $get, ?array $state): bool
    {
        $field = Budget::select('content')
            ->where('id', '=', $get('id'))
            ->first()
            ->content;

        $discount = $field['discount'] ?? null;
        $tax = $field['tax'] ?? null;

        if ($discount === null or $tax === null) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Summary of getHeaderActions
     * @return array
     */
    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make('new_budget')
                ->label(__('New Budget'))
                ->color('primary')
                ->url(route('filament.admin.resources.budgets.create'))
                ->icon('heroicon-o-currency-dollar'),

        ];
    }

    protected function getFormActions(): array
    {
        return [
            $this->getSaveFormAction(),
            $this->getDeleteFormAction(),
        ];
    }

    protected function getDeleteFormAction(): DeleteAction
    {
        return DeleteAction::make('delete')
            ->requiresConfirmation()
            ->icon('heroicon-o-trash')
            ->label(__('Delete'));
    }

    /**
     * Summary of mutateFormDataBeforeFill
     * @param array $data
     * @return array
     */
    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Verificar se já existe um array de produtos
        if (! isset($data['content']['products']) || empty($data['content']['products'])) {
            // Criar um array de produtos com base nos dados individuais
            if (isset($data['content']['product']) && isset($data['content']['product_option'])) {
                $data['content']['products'] = [
                    [
                        'product'        => $data['content']['product'],
                        'product_option' => $data['content']['product_option'],
                        'quantity'       => $data['content']['quantity'] ?? 0,
                        'location'       => $data['content']['location'] ?? null,
                        'price'          => $data['content']['price'] ?? 0,
                        'subtotal'       => ($data['content']['quantity'] ?? 0) * ($data['content']['price'] ?? 0),
                    ],
                ];
            }
        }

        return $data;
    }

    /**
     * Summary of mutateFormDataBeforeSave
     * @param array $data
     * @return array
     */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Garantir que os produtos não sejam perdidos
        if (isset($data['content']['products']) && ! empty($data['content']['products'])) {
            // Mantenha o array de produtos intacto
            // E assegure que os valores individuais também estejam atualizados para compatibilidade
            $firstProduct = $data['content']['products'][0] ?? null;

            if ($firstProduct) {
                $data['content']['product'] = $firstProduct['product'] ?? $data['content']['product'] ?? null;
                $data['content']['product_option'] = $firstProduct['product_option'] ?? $data['content']['product_option'] ?? null;
                // Não sobrescreva a quantidade total com a quantidade do primeiro produto
                // porque a quantidade total é calculada a partir de todos os produtos
            }
        } elseif (isset($data['id'])) {
            // Se os produtos desapareceram, recupere do banco de dados
            $budget = Budget::findOrFail($data['id']);
            if (isset($budget->content['products']) && ! empty($budget->content['products'])) {
                $data['content']['products'] = $budget->content['products'];
            }
        }

        return $data;
    }
}
