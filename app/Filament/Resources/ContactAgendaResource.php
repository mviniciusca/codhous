<?php

namespace App\Filament\Resources;

use App\Enums\AccessArea;
use App\Filament\Concerns\BelongsToArea;
use App\Filament\Resources\ContactAgendaResource\Pages;
use App\Filament\Resources\ContactAgendaResource\Widgets\ContactAgendaStatsOverview;
use App\Models\ContactAgenda;
use App\Models\ContactInteraction;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;
use pxlrbt\FilamentExcel\Columns\Column;
use pxlrbt\FilamentExcel\Exports\ExcelExport;

class ContactAgendaResource extends Resource
{
    use BelongsToArea;

    public static function getAccessArea(): AccessArea
    {
        return AccessArea::Comercial;
    }

    protected static ?string $model = ContactAgenda::class;

    protected static ?string $navigationIcon = 'heroicon-o-book-open';

    protected static ?string $navigationGroup = 'Empresa';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationLabel = 'Agenda de contatos';

    protected static ?string $modelLabel = 'Contato';

    protected static ?string $pluralModelLabel = 'Agenda de contatos';

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Dados Principais')
                    ->description('Insira as informações básicas para identificar e entrar em contato com a pessoa ou empresa.')
                    ->icon('heroicon-o-user-circle')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nome Completo')
                            ->prefixIcon('heroicon-o-user')
                            ->helperText('Nome da pessoa ou contato principal.')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('company')
                            ->label('Empresa')
                            ->prefixIcon('heroicon-o-building-office')
                            ->helperText('Nome da organização à qual o contato pertence.')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('position')
                            ->label('Cargo ou Função')
                            ->prefixIcon('heroicon-o-briefcase')
                            ->helperText('Cargo ocupado pela pessoa na empresa.')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('phone')
                            ->label('Telefone / WhatsApp')
                            ->prefixIcon('heroicon-o-phone')
                            ->helperText('Número com DDD para contato rápido.')
                            ->tel()
                            ->maxLength(255)
                            ->placeholder('(00) 00000-0000'),
                        Forms\Components\TextInput::make('email')
                            ->label('E-mail de Contato')
                            ->prefixIcon('heroicon-o-envelope')
                            ->helperText('Endereço de correio eletrônico principal.')
                            ->email()
                            ->maxLength(255),
                        Forms\Components\DateTimePicker::make('last_contact_at')
                            ->label('Último Contato')
                            ->prefixIcon('heroicon-o-calendar')
                            ->helperText('Data e hora do último contato realizado.'),
                        Forms\Components\Toggle::make('is_favorite')
                            ->label('Favoritar Contato')
                            ->onIcon('heroicon-m-check')
                            ->helperText('Marque como favorito para acesso rápido na agenda.')
                            ->inline(false)
                            ->default(false),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Classificação e Organização')
                    ->description('Defina como este contato deve ser agrupado e o status de relacionamento.')
                    ->icon('heroicon-o-tag')
                    ->schema([
                        Forms\Components\Select::make('category')
                            ->label('Categoria do Contato')
                            ->prefixIcon('heroicon-o-tag')
                            ->helperText('Ex: Cliente, Fornecedor, Lead, Parceiro.')
                            ->options(ContactAgenda::categoryLabels())
                            ->default(ContactAgenda::CATEGORY_CLIENT)
                            ->required()
                            ->native(false),
                        Forms\Components\Select::make('status')
                            ->label('Status do Contato')
                            ->prefixIcon('heroicon-o-signal')
                            ->helperText('Situação atual do relacionamento com o contato.')
                            ->options(ContactAgenda::statusLabels())
                            ->default(ContactAgenda::STATUS_ACTIVE)
                            ->required()
                            ->native(false),
                        Forms\Components\Select::make('source')
                            ->label('Origem do Contato')
                            ->prefixIcon('heroicon-o-globe-alt')
                            ->helperText('Como este contato chegou até você (ex: Site, Indicação).')
                            ->options(ContactAgenda::sourceLabels())
                            ->native(false)
                            ->placeholder('Selecione a origem'),
                    ])
                    ->columns(3),

                Forms\Components\Section::make('Anotações e Histórico')
                    ->description('Espaço para detalhar conversas ou informações adicionais importantes.')
                    ->icon('heroicon-o-pencil-square')
                    ->schema([
                        Forms\Components\Textarea::make('notes')
                            ->label('Observações Adicionais')
                            ->helperText('Registro de informações extras que não se encaixam nos outros campos.')
                            ->rows(4)
                            ->columnSpanFull(),
                    ])
                    ->collapsible(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label(__('Contato'))
                    ->description(fn (ContactAgenda $record): ?string => $record->position)
                    ->searchable(['name', 'position'])
                    ->sortable()
                    ->weight('medium'),
                Tables\Columns\TextColumn::make('company')
                    ->label(__('Empresa'))
                    ->searchable()
                    ->sortable()
                    ->placeholder('—'),
                Tables\Columns\TextColumn::make('category')
                    ->label(__('Categoria'))
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => ContactAgenda::categoryLabels()[$state] ?? $state)
                    ->color(fn (string $state): string => match ($state) {
                        ContactAgenda::CATEGORY_LEAD => 'info',
                        ContactAgenda::CATEGORY_CLIENT => 'success',
                        ContactAgenda::CATEGORY_SUPPLIER => 'purple',
                        ContactAgenda::CATEGORY_PARTNER => 'warning',
                        default => 'gray',
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('phone')
                    ->label(__('Telefone / E-mail'))
                    ->description(fn (ContactAgenda $record): ?string => $record->email)
                    ->searchable(['phone', 'email'])
                    ->placeholder('—'),
                Tables\Columns\TextColumn::make('last_contact_at')
                    ->label(__('Último Contato'))
                    ->dateTime('d/m/Y')
                    ->description(fn (ContactAgenda $record): ?string => $record->last_contact_at?->format('H:i'))
                    ->sortable()
                    ->placeholder('—'),
                Tables\Columns\TextColumn::make('status')
                    ->label(__('Status'))
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => ContactAgenda::statusLabels()[$state] ?? $state)
                    ->color(fn (string $state): string => match ($state) {
                        ContactAgenda::STATUS_ACTIVE => 'success',
                        ContactAgenda::STATUS_IN_CONTACT => 'info',
                        ContactAgenda::STATUS_PENDING => 'warning',
                        ContactAgenda::STATUS_NO_RESPONSE => 'danger',
                        ContactAgenda::STATUS_IN_NEGOTIATION => 'primary',
                        default => 'gray',
                    })
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_favorite')
                    ->label(__('Favorito'))
                    ->boolean()
                    ->sortable()
                    ->trueIcon('heroicon-s-star')
                    ->falseIcon('heroicon-o-star')
                    ->trueColor('warning')
                    ->falseColor('gray-300')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('source')
                    ->label(__('Origem'))
                    ->formatStateUsing(fn (?string $state): string => $state ? (ContactAgenda::sourceLabels()[$state] ?? $state) : '—')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('Criado em'))
                    ->dateTime(__('d/m/Y H:i'))
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('id', 'desc')
            ->defaultPaginationPageOption(10)
            ->paginationPageOptions([10, 25, 50])
            ->filters([
                Tables\Filters\SelectFilter::make('category')
                    ->label(__('Categoria'))
                    ->options(ContactAgenda::categoryLabels()),
                Tables\Filters\SelectFilter::make('status')
                    ->label(__('Status'))
                    ->options(ContactAgenda::statusLabels()),
                Tables\Filters\TernaryFilter::make('is_favorite')
                    ->label(__('Favoritos')),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\Action::make('whatsapp')
                        ->label(__('WhatsApp'))
                        ->icon('heroicon-o-chat-bubble-left-right')
                        ->url(fn (ContactAgenda $record): string => $record->wa_link ?? '#')
                        ->openUrlInNewTab()
                        ->visible(fn (ContactAgenda $record): bool => (bool) $record->phone),
                    Tables\Actions\Action::make('email')
                        ->label(__('E-mail'))
                        ->icon('heroicon-o-envelope')
                        ->url(fn (ContactAgenda $record): string => $record->email ? 'mailto:'.$record->email : '#')
                        ->openUrlInNewTab()
                        ->visible(fn (ContactAgenda $record): bool => (bool) $record->email),
                    Tables\Actions\Action::make('call')
                        ->label(__('Ligar'))
                        ->icon('heroicon-o-phone')
                        ->url(fn (ContactAgenda $record): string => $record->phone ? 'tel:'.preg_replace('/\D/', '', $record->phone) : '#')
                        ->visible(fn (ContactAgenda $record): bool => (bool) $record->phone),
                    Tables\Actions\Action::make('scheduleInteraction')
                        ->label(__('Agendar retorno'))
                        ->icon('heroicon-o-calendar-days')
                        ->form([
                            Forms\Components\TextInput::make('title')
                                ->label(__('Título / Assunto'))
                                ->prefixIcon('heroicon-o-document-text')
                                ->helperText('Ex: Reunião técnica, Ligação de acompanhamento.')
                                ->required(),
                            Forms\Components\Select::make('type')
                                ->label(__('Tipo de Interação'))
                                ->prefixIcon('heroicon-o-tag')
                                ->helperText('Formato do contato.')
                                ->options(ContactInteraction::typeLabels())
                                ->default(ContactInteraction::TYPE_CALL)
                                ->required()
                                ->native(false),
                            Forms\Components\DateTimePicker::make('scheduled_at')
                                ->label(__('Data e Hora'))
                                ->prefixIcon('heroicon-o-clock')
                                ->helperText('Data e horário agendados.')
                                ->default(now()->addDay()->setHour(10)->setMinute(0))
                                ->required(),
                            Forms\Components\Textarea::make('notes')
                                ->label(__('Anotações'))
                                ->helperText('Pauta ou contexto prévio.')
                                ->rows(3),
                        ])
                        ->action(function (ContactAgenda $record, array $data): void {
                            ContactInteraction::create([
                                'contact_agenda_id' => $record->id,
                                'user_id' => auth()->id(),
                                'title' => $data['title'],
                                'type' => $data['type'],
                                'scheduled_at' => $data['scheduled_at'],
                                'notes' => $data['notes'] ?? null,
                                'status' => ContactInteraction::STATUS_PENDING,
                            ]);
                        }),
                    Tables\Actions\DeleteAction::make(),
                ])->icon('heroicon-m-ellipsis-vertical'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    ExportBulkAction::make()
                        ->exports([self::excelExport()]),
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function excelExport(): ExcelExport
    {
        return ExcelExport::make('agenda-contatos')
            ->withFilename(fn () => 'agenda-contatos-'.now()->format('Y-m-d-His'))
            ->withColumns([
                Column::make('name')->heading('Nome'),
                Column::make('company')->heading('Empresa'),
                Column::make('position')->heading('Cargo'),
                Column::make('category')->heading('Categoria')->formatStateUsing(fn ($state) => ContactAgenda::categoryLabels()[$state] ?? $state),
                Column::make('status')->heading('Status')->formatStateUsing(fn ($state) => ContactAgenda::statusLabels()[$state] ?? $state),
                Column::make('phone')->heading('Telefone'),
                Column::make('email')->heading('E-mail'),
                Column::make('last_contact_at')->heading('Último Contato')->formatStateUsing(fn ($state) => $state?->format('d/m/Y H:i')),
                Column::make('created_at')->heading('Criado em')->formatStateUsing(fn ($state) => $state?->format('d/m/Y H:i')),
            ]);
    }

    public static function getWidgets(): array
    {
        return [
            ContactAgendaStatsOverview::class,
        ];
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListContactAgenda::route('/'),
            'create' => Pages\CreateContactAgenda::route('/create'),
            'edit' => Pages\EditContactAgenda::route('/{record}/edit'),
        ];
    }
}
