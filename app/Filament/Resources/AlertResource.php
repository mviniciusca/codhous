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
                Forms\Components\Grid::make(3)
                    ->schema([
                        // Coluna Principal - Conteúdo (Esquerda - 2/3)
                        Forms\Components\Group::make()
                            ->columnSpan(['default' => 3, 'lg' => 2])
                            ->schema([
                                Forms\Components\Section::make('Conteúdo da Mensagem')
                                    ->description('Escreva o texto que será exibido para os usuários.')
                                    ->icon('heroicon-o-chat-bubble-bottom-center-text')
                                    ->schema([
                                        Forms\Components\TextInput::make('title')
                                            ->label('Título do Alerta')
                                            ->helperText('Título em destaque (opcional).')
                                            ->prefixIcon('heroicon-o-bars-3-bottom-left')
                                            ->maxLength(255)
                                            ->placeholder('Ex: Atenção!'),
                                        Forms\Components\Textarea::make('message')
                                            ->label('Mensagem Principal')
                                            ->helperText('Texto descritivo que o usuário irá ler.')
                                            ->required()
                                            ->rows(5)
                                            ->columnSpanFull(),
                                        Forms\Components\Grid::make(2)
                                            ->schema([
                                                Forms\Components\TextInput::make('cta_label')
                                                    ->label('Texto do Botão')
                                                    ->helperText('Texto de ação (ex: Saiba Mais).')
                                                    ->prefixIcon('heroicon-o-cursor-arrow-rays')
                                                    ->maxLength(255)
                                                    ->placeholder('Ex: Clique aqui'),
                                                Forms\Components\TextInput::make('cta_url')
                                                    ->label('Link de Destino (URL)')
                                                    ->helperText('Para onde o usuário será levado.')
                                                    ->prefixIcon('heroicon-o-link')
                                                    ->url()
                                                    ->maxLength(500)
                                                    ->placeholder('https://...'),
                                            ]),
                                    ]),

                                Forms\Components\Section::make('Configurações de Estilo')
                                    ->description('Personalize a aparência e o local onde o alerta será exibido.')
                                    ->icon('heroicon-o-swatch')
                                    ->schema([
                                        Forms\Components\ToggleButtons::make('type')
                                            ->label('Tipo de Alerta')
                                            ->helperText('Define o propósito do alerta.')
                                            ->options(Alert::typeLabels())
                                            ->icons([
                                                Alert::TYPE_MODAL => 'heroicon-o-square-3-stack-3d',
                                                Alert::TYPE_TOAST => 'heroicon-o-bell-alert',
                                                Alert::TYPE_BANNER => 'heroicon-o-view-columns',
                                            ])
                                            ->inline()
                                            ->required()
                                            ->live()
                                            ->columnSpanFull(),
                                            
                                        Forms\Components\ToggleButtons::make('style')
                                            ->label('Cores e Estilo')
                                            ->options(Alert::styleLabels())
                                            ->colors([
                                                Alert::STYLE_INFO => 'info',
                                                Alert::STYLE_PROMO => 'primary',
                                                Alert::STYLE_ANNOUNCEMENT => 'gray',
                                                Alert::STYLE_CONSENT => 'gray',
                                                Alert::STYLE_WARNING => 'warning',
                                                Alert::STYLE_SUCCESS => 'success',
                                            ])
                                            ->icons([
                                                Alert::STYLE_INFO => 'heroicon-o-information-circle',
                                                Alert::STYLE_PROMO => 'heroicon-o-sparkles',
                                                Alert::STYLE_ANNOUNCEMENT => 'heroicon-o-megaphone',
                                                Alert::STYLE_CONSENT => 'heroicon-o-shield-check',
                                                Alert::STYLE_WARNING => 'heroicon-o-exclamation-triangle',
                                                Alert::STYLE_SUCCESS => 'heroicon-o-check-circle',
                                            ])
                                            ->inline()
                                            ->default(Alert::STYLE_INFO)
                                            ->required()
                                            ->columnSpanFull(),
                                            
                                        Forms\Components\ToggleButtons::make('position')
                                            ->label('Posição na Tela')
                                            ->options(Alert::positionLabels())
                                            ->icons([
                                                Alert::POSITION_TOP => 'heroicon-o-arrow-up',
                                                Alert::POSITION_BOTTOM => 'heroicon-o-arrow-down',
                                                Alert::POSITION_TOP_LEFT => 'heroicon-o-arrow-up-left',
                                                Alert::POSITION_TOP_RIGHT => 'heroicon-o-arrow-up-right',
                                                Alert::POSITION_BOTTOM_LEFT => 'heroicon-o-arrow-down-left',
                                                Alert::POSITION_BOTTOM_RIGHT => 'heroicon-o-arrow-down-right',
                                                Alert::POSITION_CENTER => 'heroicon-o-arrows-pointing-in',
                                            ])
                                            ->inline()
                                            ->default(Alert::POSITION_TOP)
                                            ->required()
                                            ->columnSpanFull(),
                                    ]),
                            ]),

                        // Coluna Lateral - Configurações (Direita - 1/3)
                        Forms\Components\Group::make()
                            ->columnSpan(['default' => 3, 'lg' => 1])
                            ->schema([
                                Forms\Components\Section::make('Identificação')
                                    ->schema([
                                        Forms\Components\Toggle::make('is_active')
                                            ->label('Alerta Ativo')
                                            ->helperText('Exibir o alerta no site.')
                                            ->onIcon('heroicon-m-check')
                                            ->offIcon('heroicon-m-x-mark')
                                            ->default(true),
                                            
                                        Forms\Components\TextInput::make('name')
                                            ->label('Nome Interno')
                                            ->prefixIcon('heroicon-o-tag')
                                            ->required()
                                            ->maxLength(255)
                                            ->placeholder('Ex: Banner Black Friday'),
                                            
                                        Forms\Components\TextInput::make('sort_order')
                                            ->label('Ordem de Exibição')
                                            ->prefixIcon('heroicon-o-list-bullet')
                                            ->numeric()
                                            ->default(0)
                                            ->minValue(0),
                                    ]),
                                    
                                Forms\Components\Section::make('Comportamento')
                                    ->schema([
                                        Forms\Components\Toggle::make('is_dismissible')
                                            ->label('Permitir Fechar')
                                            ->helperText('Exibe um botão "X".')
                                            ->onIcon('heroicon-m-check')
                                            ->offIcon('heroicon-m-x-mark')
                                            ->default(true),
                                            
                                        Forms\Components\Toggle::make('use_cookie')
                                            ->label('Lembrar Fechamento')
                                            ->helperText('Não mostrar de novo se fechado.')
                                            ->onIcon('heroicon-m-check')
                                            ->offIcon('heroicon-m-x-mark')
                                            ->default(false)
                                            ->live(),
                                            
                                        Forms\Components\TextInput::make('cookie_key')
                                            ->label('Chave do Cookie')
                                            ->prefixIcon('heroicon-o-key')
                                            ->maxLength(100)
                                            ->placeholder('Automático')
                                            ->visible(fn (Forms\Get $get) => (bool) $get('use_cookie')),
                                            
                                        Forms\Components\TextInput::make('cookie_duration_days')
                                            ->label('Duração (Dias)')
                                            ->prefixIcon('heroicon-o-clock')
                                            ->numeric()
                                            ->minValue(1)
                                            ->default(30)
                                            ->visible(fn (Forms\Get $get) => (bool) $get('use_cookie')),
                                    ]),
                            ]),
                    ]),
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
