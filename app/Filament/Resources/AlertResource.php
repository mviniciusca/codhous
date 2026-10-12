<?php

namespace App\Filament\Resources;

use App\Enums\AccessArea;
use App\Filament\Concerns\BelongsToArea;
use App\Filament\Resources\AlertResource\Pages;
use App\Models\Alert;
use App\Rules\SafeUrlRule;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;

/**
 * Alertas e notificações do site. Criação e edição usam o editor visual
 * (formulário à esquerda, prévia real do site à direita); a listagem é uma
 * página própria com cards (Pages\ListAlerts).
 */
class AlertResource extends Resource
{
    use BelongsToArea;

    public static function getAccessArea(): AccessArea
    {
        return AccessArea::Website;
    }

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
            ->columns(1)
            ->schema([
                Forms\Components\Grid::make(['default' => 1, 'xl' => 5])
                    ->extraAttributes(['class' => 'al-editor'])
                    ->schema([
                        Forms\Components\Group::make()
                            ->columnSpan(['default' => 1, 'xl' => 3])
                            ->schema([
                                static::getContentSection(),
                                static::getAppearanceSection(),
                                static::getBehaviorSection(),
                            ]),

                        // Prévia ao vivo e status (fixos ao rolar no desktop)
                        Forms\Components\Group::make()
                            ->columnSpan(['default' => 1, 'xl' => 2])
                            ->extraAttributes(['class' => 'al-aside'])
                            ->schema([
                                Forms\Components\View::make('filament.alerts.editor-preview'),
                                Forms\Components\View::make('filament.alerts.editor-status'),
                            ]),
                    ]),
            ]);
    }

    protected static function getContentSection(): Forms\Components\Section
    {
        return Forms\Components\Section::make('Conteúdo da mensagem')
            ->description('Defina as informações exibidas para os visitantes do site.')
            ->icon('heroicon-o-chat-bubble-bottom-center-text')
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Nome interno')
                    ->helperText('Nome para sua organização. Não aparece no site.')
                    ->prefixIcon('heroicon-m-tag')
                    ->placeholder('Ex.: Banner Black Friday')
                    ->required()
                    ->maxLength(255),

                Forms\Components\TextInput::make('title')
                    ->label('Título do alerta')
                    ->helperText('Frase em destaque no alerta. Opcional.')
                    ->prefixIcon('heroicon-m-bars-3-bottom-left')
                    ->placeholder('Ex.: Uma oferta especial para você')
                    ->maxLength(255),

                Forms\Components\Textarea::make('message')
                    ->label('Mensagem principal')
                    ->helperText('Texto que o visitante vai ler. As quebras de linha são mantidas.')
                    ->placeholder('Ex.: Confira nossas condições especiais por tempo limitado.')
                    ->required()
                    ->maxLength(1000)
                    ->rows(3)
                    ->autosize(),

                Forms\Components\Grid::make(['default' => 1, 'sm' => 2])
                    ->schema([
                        Forms\Components\TextInput::make('cta_label')
                            ->label('Texto do botão')
                            ->helperText('Ação do botão (ex.: Saiba mais). Opcional.')
                            ->prefixIcon('heroicon-m-cursor-arrow-rays')
                            ->placeholder('Ex.: Saiba mais')
                            ->maxLength(80)
                            ->requiredWith('cta_url')
                            ->validationAttribute('texto do botão'),

                        Forms\Components\TextInput::make('cta_url')
                            ->label('Link de destino')
                            ->helperText('Endereço completo (https://...) ou página do site (/contato).')
                            ->prefixIcon('heroicon-m-link')
                            ->placeholder('https://... ou /pagina')
                            ->maxLength(255)
                            ->requiredWith('cta_label')
                            ->rule(new SafeUrlRule)
                            ->validationAttribute('link de destino'),
                    ]),
            ]);
    }

    protected static function getAppearanceSection(): Forms\Components\Section
    {
        $types = Alert::typeCatalog();
        $styles = Alert::styleCatalog();
        $positions = Alert::positionCatalog();

        return Forms\Components\Section::make('Aparência')
            ->description('Formato, cores e posição do alerta na tela.')
            ->icon('heroicon-o-swatch')
            ->schema([
                Forms\Components\ToggleButtons::make('type')
                    ->label('Tipo de alerta')
                    ->helperText(fn (Get $get): string => $types[$get('type')]['description'] ?? 'Formato de exibição do alerta.')
                    ->options(Alert::typeLabels())
                    ->icons(array_map(fn (array $type): string => $type['icon'], $types))
                    ->default(Alert::TYPE_MODAL)
                    ->inline()
                    ->required()
                    ->in(array_keys($types))
                    ->live()
                    // Ao trocar o tipo, a posição volta para a padrão do novo tipo se não for compatível
                    ->afterStateUpdated(function (Get $get, Set $set, ?string $state): void {
                        if (! in_array($get('position'), Alert::positionsFor($state), true)) {
                            $set('position', Alert::defaultPositionFor($state));
                        }
                    }),

                Forms\Components\ToggleButtons::make('style')
                    ->label('Estilo')
                    ->helperText('Cor e ícone do alerta, conforme o tipo de mensagem.')
                    ->options(Alert::styleLabels())
                    ->icons(array_map(fn (array $style): string => $style['icon'], $styles))
                    ->colors(array_map(fn (array $style): string => $style['color'], $styles))
                    ->default(Alert::STYLE_INFO)
                    ->inline()
                    ->required()
                    ->in(array_keys($styles))
                    ->live(),

                // Modal é sempre centralizado: a posição só aparece para toast e banner
                Forms\Components\ToggleButtons::make('position')
                    ->label('Posição na tela')
                    ->helperText(fn (Get $get): string => $get('type') === Alert::TYPE_TOAST
                        ? 'Canto da tela onde o toast aparece.'
                        : 'Faixa no topo ou no rodapé da página.')
                    ->options(fn (Get $get): array => array_intersect_key(Alert::positionLabels(), array_flip(Alert::positionsFor($get('type')))))
                    ->icons(array_map(fn (array $position): string => $position['icon'], $positions))
                    ->default(Alert::defaultPositionFor(Alert::TYPE_MODAL))
                    ->inline()
                    ->required()
                    ->in(fn (Get $get): array => Alert::positionsFor($get('type')))
                    ->live()
                    ->hidden(fn (Get $get): bool => $get('type') === Alert::TYPE_MODAL),
            ]);
    }

    protected static function getBehaviorSection(): Forms\Components\Section
    {
        return Forms\Components\Section::make('Comportamento')
            ->description('Como o visitante interage com o alerta e quando ele é exibido.')
            ->icon('heroicon-o-adjustments-horizontal')
            ->schema([
                Forms\Components\Grid::make(['default' => 1, 'sm' => 2])
                    ->schema([
                        Forms\Components\Toggle::make('is_dismissible')
                            ->label('Permitir fechar')
                            ->helperText('Exibe o botão "X" para o visitante fechar o alerta.')
                            ->onIcon('heroicon-m-check')
                            ->offIcon('heroicon-m-x-mark')
                            ->default(true)
                            ->live(),

                        Forms\Components\Toggle::make('use_cookie')
                            ->label('Lembrar fechamento')
                            ->helperText('Depois de fechado, não aparece de novo para o mesmo visitante.')
                            ->onIcon('heroicon-m-check')
                            ->offIcon('heroicon-m-x-mark')
                            ->default(true)
                            ->live()
                            ->visible(fn (Get $get): bool => (bool) $get('is_dismissible')),
                    ]),

                Forms\Components\Grid::make(['default' => 1, 'sm' => 2])
                    ->visible(fn (Get $get): bool => $get('is_dismissible') && $get('use_cookie'))
                    ->schema([
                        Forms\Components\TextInput::make('cookie_duration_days')
                            ->label('Ocultar por')
                            ->helperText('Dias que o alerta fica oculto depois de fechado.')
                            ->prefixIcon('heroicon-m-clock')
                            ->suffix('dias')
                            ->numeric()
                            ->integer()
                            ->minValue(1)
                            ->maxValue(3650)
                            ->default(Alert::DEFAULT_COOKIE_DAYS),

                        Forms\Components\TextInput::make('cookie_key')
                            ->label('Identificador do cookie')
                            ->helperText('Deixe em branco para gerar. Trocar faz o alerta reaparecer para quem já fechou.')
                            ->prefixIcon('heroicon-m-hashtag')
                            ->placeholder('Automático')
                            ->alphaDash()
                            ->maxLength(100),
                    ]),

                Forms\Components\Grid::make(['default' => 1, 'sm' => 2])
                    ->schema([
                        Forms\Components\DateTimePicker::make('start_at')
                            ->label('Exibir a partir de')
                            ->helperText('Em branco: aparece assim que for publicado.')
                            ->prefixIcon('heroicon-m-calendar')
                            ->native(false)
                            ->displayFormat('d/m/Y H:i')
                            ->seconds(false)
                            ->live(onBlur: true),

                        Forms\Components\DateTimePicker::make('end_at')
                            ->label('Exibir até')
                            ->helperText('Em branco: fica no site até ser despublicado.')
                            ->prefixIcon('heroicon-m-calendar-days')
                            ->native(false)
                            ->displayFormat('d/m/Y H:i')
                            ->seconds(false)
                            // Só compara quando há início: o after: do Laravel usaria "agora" no lugar do vazio
                            ->rule(fn (Get $get): \Closure => function (string $attribute, mixed $value, \Closure $fail) use ($get): void {
                                if (filled($value) && filled($get('start_at')) && strtotime((string) $value) <= strtotime((string) $get('start_at'))) {
                                    $fail('O término precisa ser depois do início.');
                                }
                            })
                            ->live(onBlur: true),
                    ]),

                Forms\Components\TextInput::make('sort_order')
                    ->label('Ordem de exibição')
                    ->helperText('Com mais de um alerta ativo, os de número menor aparecem primeiro.')
                    ->prefixIcon('heroicon-m-bars-arrow-down')
                    ->numeric()
                    ->integer()
                    ->minValue(0)
                    ->maxValue(65535)
                    ->default(0),
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
