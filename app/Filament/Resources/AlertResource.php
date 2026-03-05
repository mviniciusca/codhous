<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AlertResource\Pages;
use App\Models\Alert;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class AlertResource extends Resource
{
    protected static ?string $model = Alert::class;

    protected static ?string $navigationIcon = 'heroicon-o-bell-alert';

    protected static ?string $navigationGroup = 'Website';
    protected static ?int $navigationSort = 3;

    protected static ?string $navigationLabel = 'Alertas e notificações';

    protected static ?string $modelLabel = 'Alerta';

    protected static ?string $pluralModelLabel = 'Alertas';

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Identificação')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nome (admin)')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Ex: Banner promo Black Friday'),
                        Forms\Components\Toggle::make('is_active')
                            ->label('Ativo')
                            ->default(true)
                            ->inline(),
                        Forms\Components\TextInput::make('sort_order')
                            ->label('Ordem')
                            ->numeric()
                            ->default(0)
                            ->minValue(0),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Tipo e estilo')
                    ->schema([
                        Forms\Components\Select::make('type')
                            ->label('Tipo')
                            ->options(Alert::typeLabels())
                            ->required()
                            ->live()
                            ->native(false),
                        Forms\Components\Select::make('style')
                            ->label('Estilo')
                            ->options(Alert::styleLabels())
                            ->default(Alert::STYLE_INFO)
                            ->required()
                            ->native(false),
                        Forms\Components\Select::make('position')
                            ->label('Posição')
                            ->options(Alert::positionLabels())
                            ->default(Alert::POSITION_TOP)
                            ->required()
                            ->native(false)
                            ->helperText('Onde o alerta aparece na tela (topo, rodapé, cantos, centro).'),
                    ])
                    ->columns(3),

                Forms\Components\Section::make('Conteúdo')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->label('Título')
                            ->maxLength(255)
                            ->placeholder('Opcional'),
                        Forms\Components\Textarea::make('message')
                            ->label('Mensagem')
                            ->required()
                            ->rows(3)
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('cta_label')
                            ->label('Texto do botão (CTA)')
                            ->maxLength(255)
                            ->placeholder('Ex: Saiba mais'),
                        Forms\Components\TextInput::make('cta_url')
                            ->label('Link do botão (URL)')
                            ->url()
                            ->maxLength(500)
                            ->placeholder('https://...'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Comportamento')
                    ->schema([
                        Forms\Components\Toggle::make('is_dismissible')
                            ->label('Permitir fechar')
                            ->default(true)
                            ->inline()
                            ->helperText('Usuário pode dispensar o alerta.'),
                        Forms\Components\Toggle::make('use_cookie')
                            ->label('Usar cookie ao fechar')
                            ->default(false)
                            ->live()
                            ->inline()
                            ->helperText('Ao dispensar, não exibe de novo por X dias (útil para consentimento de cookies).'),
                        Forms\Components\TextInput::make('cookie_key')
                            ->label('Chave do cookie')
                            ->maxLength(100)
                            ->placeholder('Deixe vazio para usar alert_{id}')
                            ->visible(fn (Forms\Get $get) => (bool) $get('use_cookie')),
                        Forms\Components\TextInput::make('cookie_duration_days')
                            ->label('Duração do cookie (dias)')
                            ->numeric()
                            ->minValue(1)
                            ->default(30)
                            ->visible(fn (Forms\Get $get) => (bool) $get('use_cookie')),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Período')
                    ->description('Opcional: exibir apenas em um intervalo de datas.')
                    ->schema([
                        Forms\Components\DateTimePicker::make('start_at')
                            ->label('Exibir a partir de'),
                        Forms\Components\DateTimePicker::make('end_at')
                            ->label('Exibir até'),
                    ])
                    ->columns(2)
                    ->collapsed(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Ativo')
                    ->boolean()
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Nome')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('type')
                    ->label('Tipo')
                    ->formatStateUsing(fn (string $state) => Alert::typeLabels()[$state] ?? $state)
                    ->badge()
                    ->sortable(),
                Tables\Columns\TextColumn::make('style')
                    ->label('Estilo')
                    ->formatStateUsing(fn (string $state) => Alert::styleLabels()[$state] ?? $state)
                    ->sortable(),
                Tables\Columns\TextColumn::make('position')
                    ->label('Posição')
                    ->formatStateUsing(fn (string $state) => Alert::positionLabels()[$state] ?? $state)
                    ->sortable(),
                Tables\Columns\TextColumn::make('title')
                    ->label('Título')
                    ->limit(30)
                    ->toggleable(),
                Tables\Columns\TextColumn::make('start_at')
                    ->label('Início')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('end_at')
                    ->label('Fim')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->options(Alert::typeLabels()),
                Tables\Filters\SelectFilter::make('style')
                    ->options(Alert::styleLabels()),
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Ativo')
                    ->placeholder('Todos')
                    ->trueLabel('Ativos')
                    ->falseLabel('Inativos'),
            ])
            ->actions([
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
            'index' => Pages\ListAlerts::route('/'),
            'create' => Pages\CreateAlert::route('/create'),
            'edit' => Pages\EditAlert::route('/{record}/edit'),
        ];
    }
}
