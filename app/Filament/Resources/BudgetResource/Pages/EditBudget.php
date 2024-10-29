<?php

namespace App\Filament\Resources\BudgetResource\Pages;

use App\Filament\Resources\BudgetResource;
use App\Mail\BudgetMail;
use App\Models\Budget;
use App\Models\Location;
use App\Models\Product;
use App\Models\ProductOption;
use App\Services\PdfGenerator;
use App\Services\PostcodeFinder;
use App\Services\SendBudgetMail;
use App\Trait\BudgetStatus;
use Filament\Actions;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Fieldset;
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
                                    ->label(__('Notify Mail'))
                                    ->color('success')
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
                                            return 'warning';
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
                                    ->displayFormat('d/m/Y H:i')
                                    ->default(fn () => Carbon::now()->format('Y-m-d H:i:s'))
                                    ->label(__('Date'))
                                    ->helperText(__('When this budget was created')),
                            ]),
                    ]),
                Section::make('Budget Content')
                    ->description(__('Here is the content from your budget'))
                    ->icon('heroicon-o-shopping-bag')
                    ->schema([
                        Fieldset::make(__('Customer Information'))
                            ->columns(3)
                            ->schema([
                                Group::make()
                                    ->columns(3)
                                    ->columnSpanFull()
                                    ->schema([
                                        TextInput::make('content.customer_name')
                                            ->dehydrated()
                                            ->required()
                                            ->helperText(__('Customer name'))
                                            ->default('Admin')
                                            ->label(__('Customer Name')),
                                        TextInput::make('content.customer_email')
                                            ->dehydrated()
                                            ->email()
                                            ->required()
                                            ->helperText(__('Customer email address'))
                                            ->label(__('Email')),
                                        TextInput::make('content.customer_phone')
                                            ->required()
                                            ->helperText(__('Phone Number'))
                                            ->tel()
                                            ->mask('(99)99999-9999')
                                            ->placeholder(_('(xx) XXXX-XXXX'))
                                            ->helperText(__('Customer phone number'))
                                            ->label(__('Phone')),
                                    ]),
                                TextInput::make('content.postcode')
                                    ->required()
                                    ->minLength(9)
                                    ->mask('99999-999')
                                    ->placeholder('22022-000')
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
                                    ->helperText(__('Customer street.'))
                                    ->label(__('Street')),
                                TextInput::make('content.number')
                                    ->dehydrated()
                                    ->helperText(__('Customer street number. Optional'))
                                    ->label(__('Number')),
                                TextInput::make('content.city')
                                    ->disabled()
                                    ->dehydrated()
                                    ->helperText(__('Customer city.'))
                                    ->label(__('City')),
                                TextInput::make('content.neighborhood')
                                    ->disabled()
                                    ->dehydrated()
                                    ->helperText(__('Customer neighborhood.'))
                                    ->label(__('Neighborhood')),
                                TextInput::make('content.state')
                                    ->disabled()
                                    ->dehydrated()
                                    ->helperText(__('Customer UF.'))
                                    ->label(__('State')),
                            ]),
                        Fieldset::make('Construction Components')
                            ->columns(4)
                            ->schema([
                                TextInput::make('content.quantity')
                                    ->disabled()
                                    ->dehydrated()
                                    ->label(__('Quantity m³'))
                                    ->suffix(__('m³'))
                                    ->helperText(__('Min value is 3 (ABNT NBR 7212)'))
                                    ->afterStateUpdated(fn (Set $set, string $state) => $set('quantity', $state)),
                                Select::make('content.location')
                                    ->disabled()
                                    ->dehydrated()
                                    ->label(__('Local / Area'))
                                    ->helperText(__('Local or area to be concreted'))
                                    ->options(Location::all()
                                        ->pluck('name', 'id')),
                                Select::make('content.product')
                                    ->disabled()
                                    ->dehydrated()
                                    ->label(__('Product'))
                                    ->helperText(__('Product selected'))
                                    ->options(Product::all()->pluck('name', 'id')),
                                Select::make('content.product_option')
                                    ->live()
                                    ->disabled()
                                    ->dehydrated()
                                    ->label(__('Option'))
                                    ->helperText(__('Option selected'))
                                    ->options(function (Get $get, ?string $state) {
                                        return ProductOption::where('product_id', '=', $get('content.product'))
                                            ->pluck('name', 'id');
                                    }),
                            ]),
                    ]),
                Section::make(__('Pricing'))
                    ->icon('heroicon-o-currency-dollar')
                    ->description(__('Pricing Definition & Total Cost'))
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
                            ->label(__('Price per Unity (m³)'))
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
                            ->afterStateHydrated(fn (Get $get, Set $set, ?string $state) => $this->updateBudgetStatus($get, $set, $state))
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
        $id = $get('content.product');
        $price = Product::select(['price'])
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
            Actions\DeleteAction::make()
                ->color('danger')
                ->label(__(false))
                ->icon('heroicon-o-trash'),

        ];
    }
}
