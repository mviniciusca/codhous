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
                    ->description('Defina como este alerta será identificado internamente.')
                    ->icon('heroicon-o-identification')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nome Interno')
                            ->helperText('Nome para identificação administrativa (ex: Promoção Verão 2024).')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Ex: Banner Black Friday'),
                        Forms\Components\Toggle::make('is_active')
                            ->label('Alerta Ativo')
                            ->helperText('Define se o alerta será exibido no site imediatamente.')
                            ->default(true)
                            ->inline(),
                        Forms\Components\TextInput::make('sort_order')
                            ->label('Ordem de Exibição')
                            ->helperText('Define a prioridade caso existam múltiplos alertas ativos.')
                            ->numeric()
                            ->default(0)
                            ->minValue(0),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Configurações de Estilo')
                    ->description('Personalize a aparência e o local onde o alerta será exibido.')
                    ->icon('heroicon-o-swatch')
                    ->schema([
                        Forms\Components\Select::make('type')
                            ->label('Tipo de Alerta')
                            ->helperText('Define o propósito do alerta (ex: Aviso, Promoção).')
                            ->options(Alert::typeLabels())
                            ->required()
                            ->live()
                            ->native(false),
                        Forms\Components\Select::make('style')
                            ->label('Cores e Estilo')
                            ->helperText('Esquema de cores do alerta (Sucesso, Atenção, Erro).')
                            ->options(Alert::styleLabels())
                            ->default(Alert::STYLE_INFO)
                            ->required()
                            ->native(false),
                        Forms\Components\Select::make('position')
                            ->label('Posição na Tela')
                            ->helperText('Onde o alerta aparecerá (Topo, Rodapé, Flutuante).')
                            ->options(Alert::positionLabels())
                            ->default(Alert::POSITION_TOP)
                            ->required()
                            ->native(false),
                    ])
                    ->columns(3),

                Forms\Components\Section::make('Conteúdo da Mensagem')
                    ->description('Escreva o texto que será exibido para os usuários.')
                    ->icon('heroicon-o-chat-bubble-bottom-center-text')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->label('Título do Alerta')
                            ->helperText('Título em destaque (opcional).')
                            ->maxLength(255)
                            ->placeholder('Ex: Atenção!'),
                        Forms\Components\Textarea::make('message')
                            ->label('Mensagem Principal')
                            ->helperText('Texto descritivo que o usuário irá ler.')
                            ->required()
                            ->rows(3)
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('cta_label')
                            ->label('Texto do Botão')
                            ->helperText('Texto de ação (ex: Saiba Mais, Ver Oferta).')
                            ->maxLength(255)
                            ->placeholder('Ex: Clique aqui'),
                        Forms\Components\TextInput::make('cta_url')
                            ->label('Link de Destino (URL)')
                            ->helperText('Endereço para onde o usuário será levado ao clicar.')
                            ->url()
                            ->maxLength(500)
                            ->placeholder('https://...'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Comportamento e Cookies')
                    ->description('Controle como o usuário interage e fecha o alerta.')
                    ->icon('heroicon-o-cog-8-tooth')
                    ->schema([
                        Forms\Components\Toggle::make('is_dismissible')
                            ->label('Permitir Fechar')
                            ->helperText('Exibe um botão "X" para o usuário ocultar o alerta.')
                            ->default(true)
                            ->inline(),
                        Forms\Components\Toggle::make('use_cookie')
                            ->label('Lembrar Fechamento')
                            ->helperText('Se ativado, o alerta não aparecerá novamente após ser fechado.')
                            ->default(false)
                            ->live()
                            ->inline(),
                        Forms\Components\TextInput::make('cookie_key')
                            ->label('Chave do Cookie')
                            ->helperText('Identificador único para o cookie. Deixe vazio para automático.')
                            ->maxLength(100)
                            ->placeholder('alert_black_friday')
                            ->visible(fn (Forms\Get $get) => (bool) $get('use_cookie')),
                        Forms\Components\TextInput::make('cookie_duration_days')
                            ->label('Duração (Dias)')
                            ->helperText('Por quantos dias o alerta ficará oculto após o fechamento.')
                            ->numeric()
                            ->minValue(1)
                            ->default(30)
                            ->visible(fn (Forms\Get $get) => (bool) $get('use_cookie')),
                    ])
                    ->columns(2),
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
