<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BudgetResource\Pages;
use App\Filament\Resources\BudgetResource\RelationManagers\BudgetHistoryRelationManager;
use App\Filament\Resources\BudgetResource\RelationManagers\DocumentsRelationManager;
use App\Models\Budget;
use App\Services\FakeBudgetDataService;
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
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Model;

class BudgetResource extends Resource
{
    protected static ?string $recordTitleAttribute = 'Budget';

    protected static ?string $model = Budget::class;

    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';

    public static function getGlobalSearchResultTitle(Model $record): string|Htmlable
    {
        return __('Budget');
    }

    protected static ?string $navigationGroup = 'Budget';

    public static function getGloballySearchableAttributes(): array
    {
        return ['code', 'content'];
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            __('Customer') => $record->content['customer_name'],
            __('Code')     => $record->code,
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        $count = static::getModel()::count();

        return $count != 0 ? $count : null;
    }

    public static function form(Form $form): Form
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
                                    ->inline(),
                                Select::make('status')
                                    ->helperText(__('Set the budget status'))
                                    ->options([
                                        'pending'  => __('Pending'),
                                        'on going' => __('On Going'),
                                        'done'     => __('Done'),
                                        'ignored'  => __('Ignored'),
                                    ]),
                                TextInput::make('code')
                                    ->label(__('Budget Code'))
                                    ->helperText(__('Use this code to search'))
                                    ->disabled(),
                                DateTimePicker::make('created_at')
                                    ->format('d/m/Y H:i')
                                    ->label(__('Date'))
                                    ->disabled()
                                    ->helperText(__('When this budget was created')),
                            ]),
                    ]),
                Section::make('Budget Content')
                    ->description(__('Here is the content from your budget'))
                    ->icon('heroicon-o-shopping-bag')
                    ->headerActions([
                        Action::make('fill_all_fake_data')
                            ->label(__('Fill All with Fake Data'))
                            ->icon('heroicon-o-sparkles')
                            ->color('success')
                            ->action(function (Set $set, Get $get) {
                                $fakeService = new FakeBudgetDataService();
                                $fakeData = $fakeService->generateCompleteBudgetData();

                                foreach ($fakeData as $key => $value) {
                                    $set('content.'.$key, $value);
                                }

                                Notification::make()
                                    ->title(__('Fake data generated!'))
                                    ->body(__('All fields have been filled with test data'))
                                    ->success()
                                    ->send();
                            }),
                        Action::make('clear_all_fields')
                            ->label(__('Clear All'))
                            ->icon('heroicon-o-trash')
                            ->color('danger')
                            ->requiresConfirmation()
                            ->action(function (Set $set) {
                                // Clear customer fields
                                $set('content.customer_name', '');
                                $set('content.customer_email', '');
                                $set('content.customer_phone', '');

                                // Clear address fields
                                $set('content.postcode', '');
                                $set('content.street', '');
                                $set('content.number', '');
                                $set('content.city', '');
                                $set('content.neighborhood', '');
                                $set('content.state', '');

                                Notification::make()
                                    ->title(__('All fields cleared!'))
                                    ->success()
                                    ->send();
                            }),
                    ])
                    ->schema([
                        Fieldset::make(__('Customer Information'))
                            ->columns(3)
                            ->schema([
                                Group::make()
                                    ->columns(3)
                                    ->columnSpanFull()
                                    ->schema([
                                        TextInput::make('content.customer_name')
                                            ->disabled()
                                            ->required()
                                            ->dehydrated()
                                            ->label(__('Customer Name')),
                                        TextInput::make('content.customer_email')
                                            ->disabled()
                                            ->required()
                                            ->dehydrated()
                                            ->label(__('Email')),
                                        TextInput::make('content.customer_phone')
                                            ->disabled()
                                            ->required()
                                            ->dehydrated()
                                            ->label(__('Phone')),
                                    ]),
                                TextInput::make('content.postcode')
                                    ->disabled()
                                    ->required()
                                    ->dehydrated()
                                    ->label(__('CEP')),
                                TextInput::make('content.street')
                                    ->disabled()
                                    ->dehydrated()
                                    ->required()
                                    ->label(__('Street')),
                                TextInput::make('content.number')
                                    ->disabled()
                                    ->dehydrated()
                                    ->label(__('Number')),
                                TextInput::make('content.city')
                                    ->disabled()
                                    ->required()
                                    ->dehydrated()
                                    ->label(__('City')),
                                TextInput::make('content.neighborhood')
                                    ->disabled()
                                    ->dehydrated()
                                    ->required()
                                    ->label(__('Neighborhood')),
                                TextInput::make('content.state')
                                    ->disabled()
                                    ->required()
                                    ->dehydrated()
                                    ->label(__('State')),
                            ]),
                        Fieldset::make('Construction Components')
                            ->columns(4)
                            ->schema([
                                TextInput::make('content.quantity')
                                    ->required()
                                    ->label(__('Quantity m³'))
                                    ->suffix(__('m³'))
                                    ->helperText(__('Min value is 3 (ABNT NBR 7212)'))
                                    ->afterStateHydrated(fn (Set $set, string $state) => $set('quantity', $state))
                                    ->disabled()
                                    ->dehydrated(),
                                TextInput::make('content.location')
                                    ->required()
                                    ->disabled()
                                    ->dehydrated()
                                    ->label(__('Location / Area'))
                                    ->helperText(__('Local or area to be concreted')),
                                TextInput::make('content.fck')
                                    ->required()
                                    ->label(__('FCK'))
                                    ->helperText(__('Feature Compression Know'))
                                    ->disabled()
                                    ->dehydrated(),
                                TextInput::make('content.product')
                                    ->required()
                                    ->label(__('Product'))
                                    ->helperText(__('Type of Concrete'))
                                    ->disabled()
                                    ->dehydrated(),
                            ]),
                    ])
                    ->collapsible(),
                Section::make(__('Pricing'))
                    ->icon('heroicon-o-currency-dollar')
                    ->description(__('Pricing Definition & Total Cost'))
                    ->collapsible()
                    ->columns(5)
                    ->schema([
                        TextInput::make('content.quantity')
                            ->live(onBlur: true)
                            ->dehydrated()
                            ->readonly()
                            ->required()
                            ->suffix('m³')
                            ->afterStateHydrated(function (Get $get, Set $set, ?string $state) {
                                self::calculateTotal($get, $set);
                            })
                            ->numeric(),
                        TextInput::make('content.price')
                            ->live(onBlur: true)
                            ->dehydrated()
                            ->prefix(env('CURRENCY_SUFFIX'))
                            ->label(__('Price per Unity (m³)'))
                            ->required()
                            ->numeric()
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
                            ->step(0.01)
                            ->afterStateUpdated(function (Get $get, Set $set, ?string $state) {
                                self::calculateTotal($get, $set);
                            }),
                        TextInput::make('content.discount')
                            ->live(onBlur: true)
                            ->dehydrated()
                            ->numeric()
                            ->required()
                            ->prefix('-'.env('CURRENCY_SUFFIX'))
                            ->step(0.01)
                            ->afterStateUpdated(function (Get $get, Set $set, ?string $state) {
                                self::calculateTotal($get, $set);
                            }),
                        TextInput::make('content.total')
                            ->live(onBlur: true)
                            ->dehydrated()
                            ->disabled()
                            ->numeric()
                            ->required()
                            ->prefix(env('CURRENCY_SUFFIX'))
                            ->step(0.01),
                    ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            BudgetHistoryRelationManager::class,
            DocumentsRelationManager::class,
        ];
    }

    /**
     * Summary of calculateTotal
     * @param Get $get
     * @param Set $set
     * @return void
     */
    public static function calculateTotal(Get $get, Set $set): void
    {
        $quantity = floatval($get('content.quantity') ?? 0);
        $price = floatval($get('content.price') ?? 0);
        $tax = floatval($get('content.tax') ?? 0);
        $discount = floatval($get('content.discount') ?? 0);

        $total = $quantity * $price + $tax - $discount;
        $set('content.total', number_format($total, 2, '.', ''));
    }

    /**
     * Summary of table
     * @param Table $table
     * @return Table
     */
    public static function table(Table $table): Table
    {
        return $table
            ->recordClasses(fn (Budget $record) => match ($record->status) {
                'ignored' => 'opacity-30 dark:opacity-30 hover:opacity-100 dark:hover:opacity-100',
                'done'    => 'opacity-50 dark:opacity-50 hover:opacity-100 dark:hover:opacity-100',
                default   => null,
            })
            ->columns([
                TextColumn::make('code')
                    ->searchable()
                    ->label(__('Code')),
                TextColumn::make('status')
                    ->sortable()
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending'  => 'primary',
                        'on going' => 'warning',
                        'done'     => 'success',
                        'ignored'  => 'danger'
                    }),
                TextColumn::make('content.customer_name')
                    ->sortable()
                    ->searchable()
                    ->label(__('Name')),
                TextColumn::make('content.customer_email')
                    ->label(__('Email')),
                TextColumn::make('content.customer_phone')
                    ->label(__('Phone')),
                TextColumn::make('created_at')
                    ->date('d/m/Y H:i')
                    ->sortable()
                    ->label(__('Date')),
                IconColumn::make('is_active')
                    ->label(__('Active'))
                    ->alignCenter()
                    ->boolean(),
            ])
            ->defaultSort('id', 'desc')
            ->filters([
                TernaryFilter::make('is_active')
                    ->placeholder(__('Default'))
                    ->default(true)
                    ->label(__('Show Budgets'))
                    ->trueLabel(__('Active'))
                    ->falseLabel(__('Inactive')),
                SelectFilter::make('status')
                    ->placeholder(__('All Status'))
                    ->label(__('Status'))
                    ->options([
                        'pending'  => __('Pending'),
                        'on going' => __('On Going'),
                        'done'     => __('Done'),
                        'ignored'  => __('Ignored'),
                    ])
                    ->searchable(),
            ], FiltersLayout::AboveContent)
            ->actions([
                ActionGroup::make([
                    Tables\Actions\Action::make('copy_link')
                        ->label(__('Copy PDF Link'))
                        ->icon('heroicon-o-clipboard-document')
                        ->color('success')
                        ->visible(fn (Budget $record) => ! empty($record->content['share_link']))
                        ->action(function (Budget $record) {
                            Notification::make()
                                ->title(__('PDF link copied!'))
                                ->success()
                                ->send();
                        }),
                    Tables\Actions\Action::make('generate_link')
                        ->label(__('Generate PDF Link'))
                        ->icon('heroicon-o-link')
                        ->color('primary')
                        ->visible(fn (Budget $record) => empty($record->content['share_link']))
                        ->action(function (Budget $record) {
                            // Generate new link
                            $pdfService = new \App\Services\BudgetPdfService();
                            $pdfModel = $pdfService->generatePdf($record, true);

                            if ($pdfModel) {
                                $url = $pdfModel->getDownloadUrl();
                                $content = $record->content;
                                $content['share_link'] = $url;
                                $record->update(['content' => $content]);

                                Notification::make()
                                    ->title(__('PDF share link generated successfully'))
                                    ->success()
                                    ->send();
                            } else {
                                Notification::make()
                                    ->title(__('Error generating PDF share link'))
                                    ->danger()
                                    ->send();
                            }
                        }),
                    Tables\Actions\Action::make('download_pdf')
                        ->label(__('Download PDF'))
                        ->icon('heroicon-o-document-arrow-down')
                        ->color('warning')
                        ->action(function (Budget $record) {
                            $pdfService = new \App\Services\BudgetPdfService();
                            $pdfModel = $pdfService->generatePdf($record, true);

                            if ($pdfModel && $pdfModel->fileExists()) {
                                return response()->download(
                                    $pdfModel->getFullPath(),
                                    'Budget_'.$record->code.'.pdf',
                                    ['Content-Type' => 'application/pdf']
                                );
                            }

                            Notification::make()
                                ->title(__('Error generating PDF'))
                                ->body(__('Could not generate PDF file. Please try again.'))
                                ->danger()
                                ->send();
                        }),
                    Tables\Actions\Action::make('send_email')
                        ->label(__('Send Email'))
                        ->icon('heroicon-o-envelope')
                        ->color('primary')
                        ->action(function (Budget $record) {
                            try {
                                $mail = new \App\Services\SendBudgetMail(
                                    $record->toArray(),
                                    $record->content['customer_email'] ?? '',
                                    new \App\Mail\BudgetMail($record->toArray())
                                );
                                $mail->dispatch();

                                Notification::make()
                                    ->title(__('Email sent successfully'))
                                    ->success()
                                    ->send();
                            } catch (\Exception $e) {
                                Notification::make()
                                    ->title(__('Error sending email'))
                                    ->body($e->getMessage())
                                    ->danger()
                                    ->send();
                            }
                        }),
                    Tables\Actions\Action::make('share_whatsapp')
                        ->label(__('Share on WhatsApp'))
                        ->icon('heroicon-o-phone')
                        ->color('success')
                        ->action(function (Budget $record) {
                            // Generate PDF and share link if not exists
                            if (empty($record->content['share_link'] ?? null)) {
                                $pdfService = new \App\Services\BudgetPdfService();
                                $pdfModel = $pdfService->generatePdf($record, true);

                                if ($pdfModel) {
                                    $url = $pdfModel->getDownloadUrl();
                                    $content = $record->content;
                                    $content['share_link'] = $url;
                                    $record->update(['content' => $content]);
                                }
                            }

                            $whatsApp = new \App\Services\WhatsAppShare();
                            $message = __("Hello! Here's your budget link: ").($record->content['share_link'] ?? '');
                            $url = $whatsApp->generateUrl(
                                $record->content['customer_phone'] ?? '',
                                $message
                            );

                            return redirect()->away($url);
                        }),
                    Tables\Actions\EditAction::make(),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListBudgets::route('/'),
            'create' => Pages\CreateBudget::route('/create'),
            'edit'   => Pages\EditBudget::route('/{record}/edit'),
            'bin'    => Pages\BudgetBin::route('/bin'),
        ];
    }
}
