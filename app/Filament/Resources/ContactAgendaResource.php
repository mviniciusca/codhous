<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ContactAgendaResource\Pages;
use App\Models\ContactAgenda;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ContactAgendaResource extends Resource
{
    protected static ?string $model = ContactAgenda::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationGroup = 'Empresa';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationLabel = 'Agenda de contatos';

    protected static ?string $modelLabel = 'Contato';

    protected static ?string $pluralModelLabel = 'Agenda de contatos';

    protected static ?string $recordTitleAttribute = 'name';

    public static function getNavigationBadge(): ?string
    {
        return (string) static::getModel()::count();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('Informações do contato'))
                    ->description(__('Dados principais para identificação e comunicação.'))
                    ->icon('heroicon-o-user')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label(__('Nome'))
                            ->required()
                            ->maxLength(255)
                            ->columnSpan(2),
                        Forms\Components\TextInput::make('email')
                            ->label(__('E-mail'))
                            ->email()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('phone')
                            ->label(__('Telefone'))
                            ->tel()
                            ->maxLength(255)
                            ->placeholder('(21) 99999-9999'),
                        Forms\Components\TextInput::make('company')
                            ->label(__('Empresa'))
                            ->maxLength(255),
                        Forms\Components\TextInput::make('position')
                            ->label(__('Cargo / Função'))
                            ->maxLength(255),
                        Forms\Components\Toggle::make('is_favorite')
                            ->label(__('Favorito'))
                            ->inline(false)
                            ->default(false),
                    ])
                    ->columns(2),

                Forms\Components\Section::make(__('Classificação'))
                    ->description(__('Organize por tipo e origem do contato.'))
                    ->schema([
                        Forms\Components\Select::make('category')
                            ->label(__('Categoria'))
                            ->options(ContactAgenda::categoryLabels())
                            ->default(ContactAgenda::CATEGORY_CONTACT)
                            ->required()
                            ->native(false),
                        Forms\Components\Select::make('source')
                            ->label(__('Origem'))
                            ->options(ContactAgenda::sourceLabels())
                            ->native(false)
                            ->placeholder(__('—')),
                    ])
                    ->columns(2),

                Forms\Components\Section::make(__('Anotações'))
                    ->schema([
                        Forms\Components\Textarea::make('notes')
                            ->label(__('Observações'))
                            ->rows(4)
                            ->columnSpanFull(),
                    ])
                    ->collapsed(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\IconColumn::make('is_favorite')
                    ->label('')
                    ->boolean()
                    ->sortable()
                    ->trueIcon('heroicon-s-star')
                    ->falseIcon('heroicon-o-star')
                    ->trueColor('warning')
                    ->falseColor('gray-300'),
                Tables\Columns\TextColumn::make('name')
                    ->label(__('Nome'))
                    ->searchable()
                    ->sortable()
                    ->weight('medium'),
                Tables\Columns\TextColumn::make('phone')
                    ->label(__('Telefone'))
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('email')
                    ->label(__('E-mail'))
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: false)
                    ->copyable(),
                Tables\Columns\TextColumn::make('company')
                    ->label(__('Empresa'))
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('category')
                    ->label(__('Categoria'))
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => ContactAgenda::categoryLabels()[$state] ?? $state)
                    ->color(fn (string $state): string => match ($state) {
                        ContactAgenda::CATEGORY_LEAD => 'info',
                        ContactAgenda::CATEGORY_CLIENT => 'success',
                        ContactAgenda::CATEGORY_SUPPLIER => 'warning',
                        ContactAgenda::CATEGORY_PARTNER => 'primary',
                        default => 'gray',
                    })
                    ->sortable(),
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
            ->defaultSort('name')
            ->filters([
                Tables\Filters\SelectFilter::make('category')
                    ->label(__('Categoria'))
                    ->options(ContactAgenda::categoryLabels()),
                Tables\Filters\TernaryFilter::make('is_favorite')
                    ->label(__('Favoritos')),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\Action::make('whatsapp')
                        ->label(__('WhatsApp'))
                        ->icon('heroicon-o-chat-bubble-left-right')
                        ->url(fn (ContactAgenda $record): string => $record->wa_link ?? '#')
                        ->openUrlInNewTab()
                        ->visible(fn (ContactAgenda $record): bool => (bool) $record->phone),
                    Tables\Actions\Action::make('email')
                        ->label(__('E-mail'))
                        ->icon('heroicon-o-envelope')
                        ->url(fn (ContactAgenda $record): string => $record->email ? 'mailto:' . $record->email : '#')
                        ->openUrlInNewTab()
                        ->visible(fn (ContactAgenda $record): bool => (bool) $record->email),
                    Tables\Actions\Action::make('call')
                        ->label(__('Ligar'))
                        ->icon('heroicon-o-phone')
                        ->url(fn (ContactAgenda $record): string => $record->phone ? 'tel:' . preg_replace('/\D/', '', $record->phone) : '#')
                        ->visible(fn (ContactAgenda $record): bool => (bool) $record->phone),
                ])->icon('heroicon-o-paper-airplane'),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
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
