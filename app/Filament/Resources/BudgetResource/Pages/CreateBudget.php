<?php

namespace App\Filament\Resources\BudgetResource\Pages;

use DateTime;
use Filament\Actions;
use App\Models\Setting;
use Filament\Forms\Form;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use App\Filament\Resources\BudgetResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Forms\Components\DateTimePicker;

class CreateBudget extends CreateRecord
{
    protected static string $resource = BudgetResource::class;

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Group::make()
                    ->columnSpanFull()
                    ->schema([
                        Section::make(__('Budget Overview'))
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
                                        'pending' => __('Pending'),
                                        'on going' => __('On Going'),
                                        'done' => __('Done'),
                                        'ignored' => __('Ignored'),
                                    ])
                                    ->default('pending'),
                                TextInput::make('code')

                                    ->dehydrated()
                                    ->label(__('Budget Code'))
                                    ->helperText(__('Use this code to search'))
                                    ->default('ADMIN' . rand(10000, 99999)),
                                DateTimePicker::make('created_at')
                                    ->format('d/m/Y')
                                    ->default(now())
                                    ->label(__('Date'))
                                    ->helperText(__('When this budget was created'))
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
                                            ->default('Admin')
                                            ->label(__('Customer Name')),
                                        TextInput::make('content.customer_email')
                                            ->dehydrated()
                                            ->label(__('Email')),
                                        TextInput::make('content.customer_phone')
                                            ->dehydrated()
                                            ->label(__('Phone')),
                                    ]),
                                TextInput::make('content.postcode')
                                    ->dehydrated()
                                    ->label(__('CEP')),
                                TextInput::make('content.street')
                                    ->dehydrated()
                                    ->label(__('Street')),
                                TextInput::make('content.number')
                                    ->dehydrated()
                                    ->label(__('Number')),
                                TextInput::make('content.city')
                                    ->dehydrated()
                                    ->label(__('City')),
                                TextInput::make('content.neighborhood')
                                    ->dehydrated()
                                    ->label(__('Neighborhood')),
                                TextInput::make('content.state')
                                    ->dehydrated()
                                    ->label(__('State')),
                            ]),
                        Fieldset::make('Construction Components')
                            ->columns(4)
                            ->schema([
                                TextInput::make('content.quantity')
                                    ->label(__('Quantity m³'))
                                    ->suffix(__('m³'))
                                    ->helperText(__('Min value is 3 (ABNT NBR 7212)'))
                                    ->dehydrated(),
                                Select::make('content.object')
                                    ->label(__('Local / Area'))
                                    ->helperText(__('Local or area to be concreted'))
                                    ->options(
                                        Setting::query()
                                            ->select(['budget'])
                                            ->get()
                                            ->pluck('budget.area', 'id')
                                    )
                                    ->dehydrated(),
                                Select::make('content.fck')
                                    ->label(__('FCK'))
                                    ->helperText(__('Feature Compression Know'))
                                    ->options(
                                        Setting::query()
                                            ->select(['budget'])
                                            ->get()
                                            ->pluck('budget.fck', 'id')
                                    )
                                    ->dehydrated(),
                                Select::make('content.product')
                                    ->label(__('Product'))
                                    ->helperText(__('Type of Concrete'))
                                    ->options(
                                        Setting::query()
                                            ->select(['budget'])
                                            ->get()
                                            ->pluck('budget.product', 'id')
                                    )
                                    ->dehydrated()
                            ]),
                    ]),
            ]);
    }
}
