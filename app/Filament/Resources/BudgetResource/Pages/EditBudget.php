<?php

namespace App\Filament\Resources\BudgetResource\Pages;

use App\Filament\Resources\BudgetResource;
use App\Mail\BudgetMail;
use App\Models\Budget;
use App\Models\BudgetHistory;
use App\Models\Location;
use App\Models\Product;
use App\Models\ProductOption;
use App\Services\PdfGenerator;
use App\Services\PostcodeFinder;
use App\Services\SendBudgetMail;
use App\Trait\BudgetStatus;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Carbon;
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
                                Action::make('send_mail')
                                    ->icon('heroicon-o-envelope')
                                    ->label(__('Notify Email'))
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
                                    ->requiresConfirmation()
                                    ->action(function (Get $get, ?array $state) {
                                        $mail = new SendBudgetMail($state,
                                            $get('content.customer_email'),
                                            new BudgetMail()
                                        );
                                        $mail->dispatch();
                                    }),
                                Action::make('download_pdf')
                                    ->label(__('Download PDF'))
                                    ->color(function (Get $get, ?array $state) {
                                        if (self::checkId($get, $state)) {
                                            return 'gray';
                                        } else {
                                            return 'primary';
                                        }
                                    })
                                    ->icon('heroicon-o-arrow-down-tray')
                                    ->requiresConfirmation()
                                    ->disabled(function (Get $get, ?array $state) {
                                        return self::checkId($get, $state);
                                    })
                                    ->action(function ($state) {
                                        $pdf = new PdfGenerator($state);

                                        return $pdf->generate();
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
                Section::make('Customer Information')
                    ->description(__('This section contains information about the customer.'))
                    ->icon('heroicon-o-user')
                    ->collapsible()
                    ->columns(3)
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
                        TextInput::make('content.postcode')
                            ->required()
                            ->minLength(9)
                            ->mask('99999-999')
                            ->prefixIcon('heroicon-o-map-pin')
                            ->placeholder('----- ---')
                            ->helperText(__('Customer postcode'))
                            ->maxLength(9)
                            ->suffixAction(
                                fn ($state, Set $set, $livewire) => Action::make('search-action')
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
                Section::make(__('Shopping Bag'))
                    ->description(__('Products in the shopping bag.'))
                    ->icon('heroicon-o-shopping-bag')
                    ->columns(8)
                    ->schema([
                        TextInput::make('content.quantity')
                            ->disabled()
                            ->suffix(__('m³'))
                            ->dehydrated()
                            ->label(__('Quantity'))
                            ->helperText(__('Quantity of items'))
                            ->afterStateUpdated(fn (Set $set, string $state) => $set('quantity', $state)),
                        Select::make('content.location')
                            ->disabled()
                            ->dehydrated()
                            ->label(__('Local / Area'))
                            ->prefixIcon('heroicon-o-map-pin')
                            ->columnSpan(2)
                            ->helperText(__('Local or area to be concreted'))
                            ->options(Location::all()
                                ->pluck('name', 'id')),
                        Select::make('content.product')
                            ->disabled()
                            ->dehydrated()
                            ->columnSpan(2)
                            ->prefixIcon('heroicon-o-shopping-cart')
                            ->label(__('Product'))
                            ->helperText(__('Product selected'))
                            ->options(Product::all()->pluck('name', 'id')),
                        Select::make('content.product_option')
                            ->live()
                            ->disabled()
                            ->dehydrated()
                            ->prefixIcon('heroicon-o-shopping-bag')
                            ->columnSpan(2)
                            ->label(__('Option'))
                            ->helperText(__('Option selected'))
                            ->options(function (Get $get, ?string $state) {
                                return ProductOption::where('product_id', '=', $get('content.product'))
                                    ->pluck('name', 'id');
                            }),
                        TextInput::make('content.price')
                            ->live(onBlur: true)
                            ->disabled()
                            ->dehydrated()
                            ->helperText(__('Price of product in '.env('CURRENCY_SUFFIX')))
                            ->afterStateHydrated(function (Get $get, Set $set) {
                                $this->getPrice($get, $set);
                            })
                            ->prefix(env('CURRENCY_SUFFIX'))
                            ->label(__('Price per Unity'))
                            ->required(),
                    ]),
                Section::make(__('Pricing Calculator'))
                    ->icon('heroicon-o-currency-dollar')
                    ->description(__('Pricing Definition & Total Cost.'))
                    ->columns(5)
                    ->schema([
                        TextInput::make('content.quantity')
                            ->live(onBlur: true)
                            ->disabled()
                            ->dehydrated()
                            ->required()
                            ->helperText(__('Quantity of items'))
                            ->suffix('m³')
                            ->afterStateHydrated(function (Get $get, Set $set, ?string $state) {
                                $this->calculateTotal($get, $set);
                            })
                            ->numeric(),
                        TextInput::make('content.price')
                            ->live(onBlur: true)
                            ->disabled()
                            ->dehydrated()
                            ->helperText(__('Price of product in '.env('CURRENCY_SUFFIX')))
                            ->afterStateHydrated(function (Get $get, Set $set) {
                                $this->getPrice($get, $set);
                            })
                            ->prefix(env('CURRENCY_SUFFIX'))
                            ->label(__('Price per Unity'))
                            ->required(),
                        TextInput::make('content.tax')
                            ->live(onBlur: true)
                            ->dehydrated()
                            ->prefix('+'.env('CURRENCY_SUFFIX'))
                            ->numeric()
                            ->required()
                            ->helperText(__('Sum tax or other values in '.env('CURRENCY_SUFFIX')))
                            ->default(0)
                            ->step(0.01)
                            ->afterStateHydrated(function (Get $get, Set $set, ?string $state) {
                                $this->calculateTotal($get, $set);
                                $this->updateBudgetStatus($get, $set, $state);
                            })
                            ->afterStateUpdated(function (Get $get, Set $set, ?string $state) {
                                $this->calculateTotal($get, $set);
                                $this->updateBudgetStatus($get, $set, $state);
                            }),
                        TextInput::make('content.discount')
                            ->live(onBlur: true)
                            ->dehydrated()
                            ->numeric()
                            ->required()
                            ->helperText(__('Applies a discount in '.env('CURRENCY_SUFFIX')))
                            ->prefix('-'.env('CURRENCY_SUFFIX'))
                            ->step(0.01)
                            ->afterStateHydrated(function (Get $get, Set $set, ?string $state) {
                                $this->calculateTotal($get, $set);
                                $this->updateBudgetStatus($get, $set, $state);
                            })
                            ->afterStateUpdated(function (Get $get, Set $set, ?string $state) {
                                $this->calculateTotal($get, $set);
                                $this->updateBudgetStatus($get, $set, $state);
                            }),
                        TextInput::make('content.total')
                            ->live(onBlur: true)
                            ->dehydrated()
                            ->disabled()
                            ->numeric()
                            ->required()
                            ->helperText(__('The total budget value in '.env('CURRENCY_SUFFIX')))
                            ->prefix(env('CURRENCY_SUFFIX'))
                            ->step(0.01),
                    ]),

            ]);
    }

    protected function afterSave()
    {
        BudgetHistory::create([
            'budget_id' => $this->data['id'],
            'user_id'   => Auth::user()->id,
            'action'    => 'update',
        ]);
    }

    /**
     * Summary of calculateTotal
     * @param Get $get
     * @param Set $set
     * @return void
     */
    private function calculateTotal(Get $get, Set $set): void
    {
        $quantity = floatval($get('content.quantity') ?? 0);
        $price = floatval($get('content.price') ?? 0);
        $tax = floatval($get('content.tax') ?? 0);
        $discount = floatval($get('content.discount') ?? 0);

        $total = $quantity * $price + $tax - $discount;
        $set('content.total', number_format($total, 2, '.', ''));
    }

    /**
     * Summary of getPrice
     * @param Get $get
     * @param Set $set
     * @return void
     */
    private function getPrice(Get $get, Set $set)
    {
        $id = $get('content.product_option');
        $price = ProductOption::select(['price'])
            ->where('id', '=', $id)
            ->first();
        $set('content.price', $price->price ?? 0);
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
}
