<?php

namespace App\Filament\Resources;

use App\Models\Page;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use App\Filament\Resources\PageResource\Pages;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PageResource extends Resource
{
    protected static ?string $model = Page::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-duplicate';

    protected static ?string $navigationGroup = 'Website';

    protected static ?int $navigationSort = 1;

    public static function getNavigationLabel(): string
    {
        return __('Páginas');
    }

    public static function getModelLabel(): string
    {
        return __('Página');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Páginas');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Wizard::make([
                    Forms\Components\Wizard\Step::make('Conteúdo')
                        ->description('Construa o conteúdo da sua página')
                        ->icon('heroicon-o-document-text')
                        ->schema([
                            Forms\Components\Actions::make([
                                Forms\Components\Actions\Action::make('loadTemplate')
                                    ->label('Carregar Template')
                                    ->icon('heroicon-m-sparkles')
                                    ->color('primary')
                                    ->form([
                                        Forms\Components\Select::make('template')
                                            ->label('Escolha o Template')
                                            ->options([
                                                'landing_vendas' => 'Landing Page de Vendas',
                                                'institucional' => 'Página Institucional Padrão',
                                                'contato' => 'Página de Contato',
                                            ])
                                            ->required(),
                                        Forms\Components\Radio::make('behavior')
                                            ->label('Como deseja inserir?')
                                            ->options([
                                                'append' => 'Adicionar ao final (Manter blocos atuais)',
                                                'replace' => 'Substituir tudo (Apagar blocos atuais)',
                                            ])
                                            ->default('append')
                                            ->required(),
                                    ])
                                    ->action(function (Set $set, \Filament\Forms\Get $get, array $data) {
                                        $blocks = [];

                                        if ($data['template'] === 'landing_vendas') {
                                            $blocks = [
                                                ['type' => 'page_header', 'data' => ['title' => 'Landing Page de Vendas', 'badge' => 'OFERTA']],
                                                ['type' => 'services', 'data' => ['title' => 'Nossos Serviços']],
                                                ['type' => 'module_reference', 'data' => []], // Módulo Global (Formulário, por exemplo)
                                                ['type' => 'faq', 'data' => ['title' => 'Perguntas Frequentes']],
                                            ];
                                        } elseif ($data['template'] === 'institucional') {
                                            $blocks = [
                                                ['type' => 'page_header', 'data' => ['title' => 'Sobre a Empresa', 'badge' => 'QUEM SOMOS']],
                                                ['type' => 'image_with_text', 'data' => ['title' => 'Nossa História']],
                                                ['type' => 'stats', 'data' => []],
                                                ['type' => 'differentials', 'data' => ['title' => 'Nossos Diferenciais']],
                                            ];
                                        } elseif ($data['template'] === 'contato') {
                                            $blocks = [
                                                ['type' => 'page_header', 'data' => ['title' => 'Fale Conosco', 'badge' => 'CONTATO']],
                                                ['type' => 'contact_banner', 'data' => ['title' => 'Atendimento']],
                                                ['type' => 'map', 'data' => ['title' => 'Nossa Localização']],
                                            ];
                                        }

                                        $state = $data['behavior'] === 'replace' ? [] : ($get('content') ?? []);

                                        foreach ($blocks as $block) {
                                            $state[(string) \Illuminate\Support\Str::uuid()] = $block;
                                        }

                                        $set('content', $state);
                                    }),
                            ]),
                            Forms\Components\Builder::make('content')
                                ->label('')
                                ->addActionLabel(__('Adicionar Novo Bloco'))
                                ->blocks([
                                    self::getPageHeaderBlock(),
                                    self::getServicesBlock(),
                                    self::getDifferentialsBlock(),
                                    self::getShowcaseBlock(),
                                    self::getEquipmentShowcaseBlock(),
                                    self::getPartnersBlock(),
                                    self::getTimelineBlock(),
                                    self::getFaqBlock(),
                                    self::getTestimonialsBlock(),
                                    self::getCoverageBlock(),
                                    self::getCtaBlock(),
                                    self::getContactBannerBlock(),
                                    self::getMapBlock(),
                                    self::getRichTextBlock(),
                                    self::getPaymentOfferBlock(),
                                    self::getModuleReferenceBlock(),
                                    self::getStatsBlock(),
                                    self::getImageWithTextBlock(),
                                    self::getDataTableBlock(),
                                    self::getFeaturedTestimonialBlock(),
                                    self::getCardsBlock(),
                                ])
                                ->collapsible()
                                ->collapsed()
                                ->cloneable()
                                ->blockPickerColumns(2)
                                ->blockNumbers(false)
                                ->blockPreviews()
                                ->editAction(fn (\Filament\Forms\Components\Actions\Action $action) => $action->modalWidth('7xl')),
                        ]),

                    Forms\Components\Wizard\Step::make('Configurações e SEO')
                        ->description('Configure a página para os motores de busca')
                        ->icon('heroicon-o-cog-6-tooth')
                        ->schema([
                            Forms\Components\Grid::make(2)
                                ->schema([
                                    Forms\Components\Section::make(__('Configurações Básicas'))
                                        ->description(__('Informações essenciais para a publicação e identificação da página.'))
                                        ->icon('heroicon-o-adjustments-horizontal')
                                        ->schema([
                                            Forms\Components\TextInput::make('title')
                                                ->label(__('Título da Página'))
                                                ->helperText(__('O título principal que aparecerá na guia do navegador e nos resultados de busca.'))
                                                ->required()
                                                ->lazy()
                                                ->afterStateUpdated(fn (Set $set, ?string $state) => $set('slug', Str::slug($state))),
                                            Forms\Components\TextInput::make('slug')
                                                ->label(__('Slug (URL)'))
                                                ->helperText(__('O caminho da URL para esta página (ex: /sobre-nos).'))
                                                ->required()
                                                ->unique(ignoreRecord: true, modifyRuleUsing: fn (\Illuminate\Validation\Rules\Unique $rule) => $rule->withoutTrashed()),
                                            Forms\Components\Grid::make(2)
                                                ->schema([
                                                    Forms\Components\Toggle::make('is_visible')
                                                        ->label(__('Publicar Página'))
                                                        ->helperText(__('Ative para tornar esta página pública.'))
                                                        ->onIcon('heroicon-m-check')
                                                        ->default(true),
                                                    Forms\Components\Toggle::make('is_active_in_menu')
                                                        ->label(__('Aparecer no Menu'))
                                                        ->helperText(__('Adiciona automaticamente a página no menu de navegação do topo.'))
                                                        ->onIcon('heroicon-m-bars-3')
                                                        ->default(true),
                                                    Forms\Components\TextInput::make('sort_order')
                                                        ->label(__('Ordem'))
                                                        ->helperText(__('Ordem de exibição em menus.'))
                                                        ->numeric()
                                                        ->default(0),
                                                ]),
                                        ])->columnSpan(1),

                                    Forms\Components\Section::make(__('Otimização de Busca (SEO)'))
                                        ->description(__('Ajustes finos para melhorar o ranqueamento no Google e redes sociais.'))
                                        ->icon('heroicon-o-magnifying-glass-circle')
                                        ->schema([
                                            Forms\Components\TextInput::make('meta.title')
                                                ->label(__('Título SEO (Opcional)'))
                                                ->helperText(__('Se deixado em branco, será usado o Título da Página.')),
                                            Forms\Components\Textarea::make('meta.description')
                                                ->label(__('Meta Descrição'))
                                                ->helperText(__('Resumo exibido nos resultados do Google. Recomendado até 160 caracteres.'))
                                                ->rows(3),
                                            Forms\Components\TextInput::make('meta.keywords')
                                                ->label(__('Palavras-chave'))
                                                ->helperText(__('Ex: serviços, produtos, empresa. Separadas por vírgula.')),
                                            Forms\Components\FileUpload::make('meta.og_image')
                                                ->label(__('Imagem de Compartilhamento (OG Image)'))
                                                ->helperText(__('Imagem que aparecerá ao compartilhar a URL no WhatsApp, Facebook, etc.'))
                                                ->image()
                                                ->directory('seo'),
                                        ])->columnSpan(1),
                                ]),
                        ]),
                ])
                ->skippable()
                ->columnSpanFull()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label(__('Título'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('slug')
                    ->label(__('Slug'))
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('gray'),

                Tables\Columns\IconColumn::make('is_visible')
                    ->label(__('Visível'))
                    ->boolean()
                    ->sortable(),
                Tables\Columns\TextColumn::make('sort_order')
                    ->label(__('Ordem'))
                    ->sortable(),
            ])
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\RestoreAction::make(),
                Tables\Actions\ForceDeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPages::route('/'),
            'create' => Pages\CreatePage::route('/create'),
            'edit' => Pages\EditPage::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    // Block Definitions

    protected static function getBlockTabs(array $contentSchema, array $styleSchema = [], array $advancedSchema = []): array
    {
        return [
            \Filament\Forms\Components\Tabs::make('Tabs')
                ->tabs([
                    \Filament\Forms\Components\Tabs\Tab::make('Conteúdo')
                        ->icon('heroicon-o-document-text')
                        ->schema($contentSchema),
                    \Filament\Forms\Components\Tabs\Tab::make('Estilo')
                        ->icon('heroicon-o-paint-brush')
                        ->schema(array_merge([
                            \Filament\Forms\Components\ToggleButtons::make('background_color')
                                ->label(__('Cor de Fundo da Seção'))
                                ->helperText(__('Escolha a cor predominante no fundo deste bloco.'))
                                ->options([
                                    'bg-white' => 'Branco',
                                    'bg-background' => 'Padrão',
                                    'bg-muted/30' => 'Cinza Claro',
                                    'bg-primary' => 'Primária',
                                    'bg-foreground' => 'Escuro',
                                ])
                                ->icons([
                                    'bg-white' => 'heroicon-o-sun',
                                    'bg-background' => 'heroicon-o-stop',
                                    'bg-muted/30' => 'heroicon-o-stop',
                                    'bg-primary' => 'heroicon-o-star',
                                    'bg-foreground' => 'heroicon-o-moon',
                                ])
                                ->default('bg-white')
                                ->inline(),
                            \Filament\Forms\Components\ToggleButtons::make('text_color')
                                ->label(__('Esquema de Cores (Texto)'))
                                ->helperText(__('Define se os textos devem ser escuros ou brancos para contrastar com o fundo.'))
                                ->options([
                                    'light' => 'Claro (Texto Escuro)',
                                    'dark' => 'Escuro (Texto Branco)',
                                ])
                                ->icons([
                                    'light' => 'heroicon-o-sun',
                                    'dark' => 'heroicon-o-moon',
                                ])
                                ->default('light')
                                ->inline(),
                        ], $styleSchema)),
                    \Filament\Forms\Components\Tabs\Tab::make('Avançado')
                        ->icon('heroicon-o-cog-8-tooth')
                        ->schema(array_merge([
                            \Filament\Forms\Components\TextInput::make('custom_id')
                                ->label(__('ID da Seção (HTML)'))
                                ->prefixIcon('heroicon-o-hashtag')
                                ->helperText(__('Útil para links âncora no menu. Ex: "sobre-nos"')),
                            \Filament\Forms\Components\TextInput::make('custom_css_classes')
                                ->label(__('Classes CSS Extras'))
                                ->prefixIcon('heroicon-o-code-bracket')
                                ->helperText(__('Classes do Tailwind para desenvolvedores fazerem ajustes finos. Ex: "pb-0 pt-32"')),
                        ], $advancedSchema)),
                ])
                ->contained(false)
        ];
    }

    protected static function getPartnersBlock(): Forms\Components\Builder\Block
    {
        return Forms\Components\Builder\Block::make('partners')
            ->preview('filament.block-previews.partners')
            ->label(__('Parceiros'))
            ->icon('heroicon-o-building-office')
            ->schema(self::getBlockTabs([
                Forms\Components\TextInput::make('title')->label(__('Título'))->helperText(__('Título da seção de parceiros.')),
                Forms\Components\Textarea::make('description')->label(__('Descrição'))->helperText(__('Breve texto sobre a parceria.')),
                Forms\Components\Repeater::make('items')
                    ->label(__('Parceiros'))
                    ->helperText(__('Cadastre as logomarcas ou nomes dos parceiros.'))
                    ->schema([
                        Forms\Components\TextInput::make('name')->label(__('Nome'))->helperText(__('Nome da empresa parceira.'))->required(),
                        Forms\Components\TextInput::make('icon')->label(__('Ícone (Lucide)'))->helperText(__('Nome do ícone Lucide, se houver.')),
                    ])->columns(2),
            ]));
    }

    protected static function getServicesBlock(): Forms\Components\Builder\Block
    {
        return Forms\Components\Builder\Block::make('services')
            ->preview('filament.block-previews.services')
            ->label(__('Serviços'))
            ->icon('heroicon-o-wrench-screwdriver')
            ->schema(self::getBlockTabs([
                Forms\Components\TextInput::make('title')->label(__('Título'))->helperText(__('Título principal da área de serviços.')),
                Forms\Components\Textarea::make('description')->label(__('Descrição'))->helperText(__('Descrição geral sobre os serviços oferecidos.')),
                Forms\Components\Repeater::make('items')
                    ->label(__('Serviços'))
                    ->helperText(__('Cadastre os serviços que deseja exibir.'))
                    ->schema([
                        Forms\Components\TextInput::make('title')->label(__('Título'))->helperText(__('Nome do serviço.'))->required(),
                        Forms\Components\Textarea::make('description')->label(__('Descrição'))->helperText(__('Detalhes sobre o serviço.')),
                        Forms\Components\TextInput::make('icon')->label(__('Ícone'))->helperText(__('Nome do ícone representativo.')),
                        Forms\Components\TagsInput::make('bullets')->label(__('Tópicos'))->helperText(__('Pressione Enter para adicionar tópicos (tags).')),
                        Forms\Components\TextInput::make('cta_label')->label(__('Rótulo do Botão (CTA)'))->helperText(__('Texto do botão de ação do serviço.')),
                        Forms\Components\TextInput::make('cta_url')->label(__('URL do Botão (CTA)'))->helperText(__('Link para a página do serviço.')),
                    ]),
            ]));
    }

    protected static function getTimelineBlock(): Forms\Components\Builder\Block
    {
        return Forms\Components\Builder\Block::make('timeline')
            ->preview('filament.block-previews.timeline')
            ->label(__('Linha do Tempo (Etapas)'))
            ->icon('heroicon-o-clock')
            ->schema(self::getBlockTabs([
                Forms\Components\TextInput::make('title')->label(__('Título'))->helperText(__('Título para a linha do tempo.')),
                Forms\Components\Repeater::make('steps')
                    ->label(__('Etapas'))
                    ->helperText(__('Cadastre cada passo da linha do tempo.'))
                    ->schema([
                        Forms\Components\TextInput::make('step_label')->label(__('Rótulo da Etapa'))->helperText(__('Ex: Passo 1, Ano 2023.'))->required(),
                        Forms\Components\TextInput::make('title')->label(__('Título'))->helperText(__('Título da etapa.'))->required(),
                        Forms\Components\Textarea::make('description')->label(__('Descrição'))->helperText(__('Explicação da etapa.')),
                        Forms\Components\TextInput::make('icon')->label(__('Ícone'))->helperText(__('Ícone representativo da etapa.')),
                    ]),
            ]));
    }

    protected static function getShowcaseBlock(): Forms\Components\Builder\Block
    {
        return Forms\Components\Builder\Block::make('showcase')
            ->preview('filament.block-previews.showcase')
            ->label(__('Galeria de Projetos / Portfólio'))
            ->icon('heroicon-o-camera')
            ->schema(self::getBlockTabs([
                Forms\Components\Grid::make(2)->schema([
                    Forms\Components\TextInput::make('badge')->label(__('Pré-título'))->helperText(__('Ex: NOSSAS OBRAS'))->placeholder('NOSSAS OBRAS'),
                    Forms\Components\TextInput::make('title')->label(__('Título'))->helperText(__('Título principal da galeria.'))->required(),
                    Forms\Components\Textarea::make('description')->label(__('Descrição'))->helperText(__('Breve texto explicativo da galeria.'))->columnSpanFull(),
                    Forms\Components\TextInput::make('limit')->numeric()->default(4)->label(__('Limite de itens'))->helperText(__('Quantidade máxima de obras a serem exibidas.')),
                ])
            ]));
    }

    protected static function getEquipmentShowcaseBlock(): Forms\Components\Builder\Block
    {
        return Forms\Components\Builder\Block::make('equipment_showcase')
            ->preview('filament.block-previews.equipment_showcase')
            ->label(__('Catálogo de Itens / Showcase'))
            ->icon('heroicon-o-truck')
            ->schema(self::getBlockTabs([
                Forms\Components\Grid::make(2)->schema([
                    Forms\Components\TextInput::make('badge')->label(__('Pré-título'))->helperText(__('Ex: EQUIPAMENTOS'))->placeholder('EQUIPAMENTOS'),
                    Forms\Components\TextInput::make('title')->label(__('Título'))->helperText(__('Título principal da vitrine de equipamentos.'))->required(),
                    Forms\Components\Textarea::make('description')->label(__('Descrição'))->helperText(__('Breve texto explicativo.'))->columnSpanFull(),
                ])
            ]));
    }

    protected static function getFaqBlock(): Forms\Components\Builder\Block
    {
        return Forms\Components\Builder\Block::make('faq')
            ->preview('filament.block-previews.faq')
            ->label(__('FAQ (Perguntas Frequentes)'))
            ->icon('heroicon-o-question-mark-circle')
            ->schema(self::getBlockTabs([
                Forms\Components\TextInput::make('title')->label(__('Título'))->helperText(__('Título da seção de perguntas frequentes.')),
                Forms\Components\Repeater::make('items')
                    ->label(__('Perguntas'))
                    ->helperText(__('Cadastre as perguntas e respostas.'))
                    ->schema([
                        Forms\Components\TextInput::make('question')->label(__('Pergunta'))->helperText(__('A dúvida frequente.'))->required(),
                        Forms\Components\Textarea::make('answer')->label(__('Resposta'))->helperText(__('A resposta para a dúvida.'))->required(),
                    ]),
            ]));
    }

    protected static function getTestimonialsBlock(): Forms\Components\Builder\Block
    {
        return Forms\Components\Builder\Block::make('testimonials')
            ->preview('filament.block-previews.testimonials')
            ->label(__('Depoimentos'))
            ->icon('heroicon-o-chat-bubble-bottom-center-text')
            ->schema(self::getBlockTabs([
                Forms\Components\TextInput::make('title')->label(__('Título'))->helperText(__('Título da seção de depoimentos.')),
                Forms\Components\Repeater::make('items')
                    ->label(__('Depoimentos'))
                    ->helperText(__('Adicione os relatos de clientes.'))
                    ->schema([
                        Forms\Components\Textarea::make('quote')->label(__('Citação'))->helperText(__('O texto do depoimento.'))->required(),
                        Forms\Components\TextInput::make('author_name')->label(__('Nome do Autor'))->helperText(__('Nome de quem fez o depoimento.'))->required(),
                        Forms\Components\TextInput::make('author_role')->label(__('Cargo / Empresa'))->helperText(__('Cargo ou empresa do autor.')),
                        Forms\Components\TextInput::make('stars')->label(__('Estrelas'))->helperText(__('Quantidade de estrelas (ex: 5).'))->numeric()->default(5),
                    ]),
            ]));
    }

    protected static function getCoverageBlock(): Forms\Components\Builder\Block
    {
        return Forms\Components\Builder\Block::make('coverage')
            ->preview('filament.block-previews.coverage')
            ->label(__('Área de Atendimento'))
            ->icon('heroicon-o-map-pin')
            ->schema(self::getBlockTabs([
                Forms\Components\TextInput::make('title')->label(__('Título'))->helperText(__('Título da seção de área de cobertura.')),
                Forms\Components\Select::make('cities')
                    ->label(__('Cidades Atendidas'))
                    ->multiple()
                    ->options(\App\Models\OperationArea::query()->where('is_active', true)->pluck('city', 'city'))
                    ->helperText(__('Selecione as cidades que deseja destacar. Os dados vêm do módulo de Áreas de Operação.')),
            ]));
    }

    protected static function getCtaBlock(): Forms\Components\Builder\Block
    {
        return Forms\Components\Builder\Block::make('cta')
            ->preview('filament.block-previews.cta')
            ->label(__('Chamada para Ação (CTA)'))
            ->icon('heroicon-o-megaphone')
            ->schema(self::getBlockTabs([
                Forms\Components\TextInput::make('title')->label(__('Título'))->helperText(__('Título da chamada principal.')),
                Forms\Components\Textarea::make('subtitle')->label(__('Subtítulo'))->helperText(__('Texto de apoio da chamada.')),
                Forms\Components\TextInput::make('button_label')->label(__('Rótulo do Botão'))->helperText(__('Texto do botão de ação.')),
                Forms\Components\TextInput::make('button_url')->label(__('URL do Botão'))->helperText(__('Link para onde o botão deve levar.')),
            ]));
    }

    protected static function getDifferentialsBlock(): Forms\Components\Builder\Block
    {
        return Forms\Components\Builder\Block::make('differentials')
            ->preview('filament.block-previews.differentials')
            ->label(__('Diferenciais / Recursos'))
            ->icon('heroicon-o-shield-check')
            ->schema(self::getBlockTabs([
                Forms\Components\TextInput::make('subtitle')->label(__('Subtítulo'))->helperText(__('Texto pequeno acima do título.')),
                Forms\Components\TextInput::make('title')->label(__('Título Principal'))->helperText(__('Título de destaque da seção.')),
                Forms\Components\Textarea::make('description')->label(__('Descrição / Texto de Apoio'))->helperText(__('Explicação geral dos diferenciais.')),
                Forms\Components\Repeater::make('items')
                    ->label(__('Itens (Recomendado: 3)'))
                    ->helperText(__('Adicione os pilares ou diferenciais.'))
                    ->schema([
                        Forms\Components\TextInput::make('title')->label(__('Título'))->helperText(__('Nome do diferencial.'))->required(),
                        Forms\Components\Textarea::make('description')->label(__('Descrição'))->helperText(__('Explicação do diferencial.'))->required(),
                        Forms\Components\TextInput::make('icon')->label(__('Ícone (Lucide)'))->helperText(__('Ícone representativo.'))->default('check-circle'),
                    ])->columns(2),
            ]));
    }

    protected static function getPageHeaderBlock(): Forms\Components\Builder\Block
    {
        return Forms\Components\Builder\Block::make('page_header')
            ->preview('filament.block-previews.page_header')
            ->label(__('Cabeçalho da Página'))
            ->icon('heroicon-o-document-text')
            ->schema(self::getBlockTabs([
                Forms\Components\Grid::make(2)->schema([
                    Forms\Components\TextInput::make('badge')->label(__('Pré-título'))->helperText(__('Texto acima do título principal.'))->placeholder('NOSSOS SERVIÇOS'),
                    Forms\Components\TextInput::make('title')->label(__('Título Principal'))->helperText(__('Título grande da página.'))->required(),
                    Forms\Components\Textarea::make('description')->label(__('Descrição'))->helperText(__('Subtítulo ou texto descritivo do cabeçalho.'))->columnSpanFull(),
                    Forms\Components\Toggle::make('show_breadcrumbs')->label(__('Mostrar Breadcrumbs'))->helperText(__('Exibe o caminho de navegação (ex: Home > Serviços).'))->onIcon('heroicon-m-check')->default(true)->columnSpanFull(),
                    Forms\Components\FileUpload::make('background_image')->image()->directory('headers')->label(__('Imagem de Fundo (Opcional)'))->helperText(__('Imagem de fundo para o cabeçalho.'))->columnSpanFull(),
                ])
            ]));
    }


    protected static function getStatsBlock(): Forms\Components\Builder\Block
    {
        return Forms\Components\Builder\Block::make('stats')
            ->preview('filament.block-previews.stats')
            ->label(__('Estatísticas'))
            ->icon('heroicon-o-chart-bar-square')
            ->schema(self::getBlockTabs([
                Forms\Components\Repeater::make('items')
                    ->label(__('Itens (Estatísticas)'))
                    ->helperText(__('Adicione os números e textos explicativos (ex: 13.800+ Colaboradores). Recomendado até 4 itens.'))
                    ->schema([
                        Forms\Components\TextInput::make('value')
                            ->label(__('Valor (Número)'))
                            ->placeholder('Ex: 13.800+')
                            ->required(),
                        Forms\Components\TextInput::make('label')
                            ->label(__('Rótulo (Texto)'))
                            ->placeholder('Ex: Colaboradores')
                            ->required(),
                        Forms\Components\TextInput::make('icon')
                            ->label(__('Ícone (Lucide)'))
                            ->default('users')
                            ->helperText('Busque em lucide.dev/icons'),
                    ])
                    ->columns(3)
                    ->cloneable()
                    ->collapsible()
                    ->maxItems(4)
                    ->defaultItems(4),
            ]));
    }

    protected static function getIconSelection(string $name, string $label, string $default = 'arrow-right'): array
    {
        return [
            Forms\Components\Group::make()->schema([
                Forms\Components\ToggleButtons::make("{$name}_select")
                    ->label($label)
                    ->helperText(__('Escolha um ícone rápido ou selecione "Outro".'))
                    ->options([
                        'arrow-right' => 'Seta',
                        'play' => 'Play',
                        'check-circle' => 'Check',
                        'star' => 'Estrela',
                        'zap' => 'Raio',
                        'other' => 'Outro',
                    ])
                    ->icons([
                        'arrow-right' => 'heroicon-o-arrow-right',
                        'play' => 'heroicon-o-play',
                        'check-circle' => 'heroicon-o-check-circle',
                        'star' => 'heroicon-o-star',
                        'zap' => 'heroicon-o-bolt',
                        'other' => 'heroicon-o-ellipsis-horizontal-circle',
                    ])
                    ->inline()
                    ->live()
                    ->default(in_array($default, ['arrow-right', 'play', 'check-circle', 'star', 'zap']) ? $default : 'other'),
                Forms\Components\TextInput::make("{$name}_custom")
                    ->label(__('Nome do Ícone Lucide'))
                    ->helperText(__('Digite o nome (ex: shield). Veja lucide.dev/icons'))
                    ->prefixIcon('heroicon-o-magnifying-glass')
                    ->visible(fn (\Filament\Forms\Get $get) => $get("{$name}_select") === 'other')
                    ->required(fn (\Filament\Forms\Get $get) => $get("{$name}_select") === 'other')
                    ->default(!in_array($default, ['arrow-right', 'play', 'check-circle', 'star', 'zap']) ? $default : ''),
            ])->columnSpanFull()
        ];
    }

    protected static function getImageWithTextBlock(): Forms\Components\Builder\Block
    {
        return Forms\Components\Builder\Block::make('image_with_text')
            ->preview('filament.block-previews.image_with_text')
            ->label(__('Seção de Imagem + Texto'))
            ->icon('heroicon-o-photo')
            ->schema(self::getBlockTabs([
                Forms\Components\Grid::make(1)->schema([
                    Forms\Components\Section::make('Conteúdo Principal')->schema([
                        ...self::getIconSelection('badge_icon', 'Ícone do Badge', 'zap'),
                        Forms\Components\TextInput::make('badge')
                            ->label(__('Badge (Ex: Sobre Nós)'))
                            ->helperText(__('Pequeno texto de destaque acima do título.'))
                            ->prefixIcon('heroicon-o-tag')
                            ->placeholder('SOBRE NÓS'),
                        
                        Forms\Components\TextInput::make('title')
                            ->label(__('Título Principal'))
                            ->helperText(__('O título grande da seção.'))
                            ->prefixIcon('heroicon-o-h1')
                            ->required(),
                            
                        Forms\Components\Textarea::make('description')
                            ->label(__('Descrição / Texto de Apoio'))
                            ->helperText(__('O texto principal descrevendo os detalhes. Aceita múltiplas linhas.'))
                            ->rows(4),
                    ])->collapsible(),

                    Forms\Components\Section::make('Botão Primário')->schema([
                        Forms\Components\Grid::make(2)->schema([
                            Forms\Components\TextInput::make('button_text')
                                ->label(__('Texto do Botão'))
                                ->helperText(__('Ex: Começar Agora'))
                                ->prefixIcon('heroicon-o-cursor-arrow-rays'),
                            Forms\Components\TextInput::make('button_url')
                                ->label(__('URL do Botão'))
                                ->helperText(__('Link para onde o botão leva.'))
                                ->prefixIcon('heroicon-o-link'),
                        ]),
                        ...self::getIconSelection('button_icon', 'Ícone do Botão Primário', 'arrow-right'),
                    ])->collapsible()->collapsed(),

                    Forms\Components\Section::make('Botão Secundário')->schema([
                        Forms\Components\Grid::make(2)->schema([
                            Forms\Components\TextInput::make('secondary_button_text')
                                ->label(__('Texto do Botão'))
                                ->helperText(__('Ex: Ver Demonstração'))
                                ->prefixIcon('heroicon-o-cursor-arrow-rays'),
                            Forms\Components\TextInput::make('secondary_button_url')
                                ->label(__('URL do Botão'))
                                ->helperText(__('Link para onde o botão leva.'))
                                ->prefixIcon('heroicon-o-link'),
                        ]),
                        ...self::getIconSelection('secondary_button_icon', 'Ícone do Botão Secundário', 'play'),
                    ])->collapsible()->collapsed(),

                    Forms\Components\Section::make('Mini Cards (Rodapé)')->schema([
                        Forms\Components\Repeater::make('mini_stats')
                            ->label(__('Mini Cards de Estatísticas/Features'))
                            ->helperText(__('Adicione pequenos itens de destaque abaixo dos botões.'))
                            ->schema([
                                ...self::getIconSelection('icon', 'Ícone do Card', 'check-circle'),
                                Forms\Components\TextInput::make('title')
                                    ->label(__('Título'))
                                    ->helperText(__('Ex: Mais Produtividade'))
                                    ->prefixIcon('heroicon-o-h3')
                                    ->required(),
                                Forms\Components\TextInput::make('subtitle')
                                    ->label(__('Subtítulo'))
                                    ->helperText(__('Ex: Descrição curta do card.'))
                                    ->prefixIcon('heroicon-o-bars-3-bottom-left'),
                            ])->collapsible()->cloneable(),
                    ])->collapsible()->collapsed(),

                    Forms\Components\Section::make('Imagem')->schema([
                        Forms\Components\FileUpload::make('image')
                            ->label(__('Imagem Principal'))
                            ->helperText(__('Faça o upload da imagem da seção.'))
                            ->image()
                            ->directory('blocks')
                            ->required(),
                        Forms\Components\ToggleButtons::make('image_position')
                            ->label(__('Posição da Imagem'))
                            ->helperText(__('Deseja a imagem na direita ou na esquerda?'))
                            ->options([
                                'left' => 'Esquerda', 
                                'right' => 'Direita'
                            ])
                            ->icons([
                                'left' => 'heroicon-o-bars-3-bottom-left',
                                'right' => 'heroicon-o-bars-3-bottom-right',
                            ])
                            ->default('right')
                            ->inline(),
                        Forms\Components\ToggleButtons::make('image_vertical_alignment')
                            ->label(__('Alinhamento Vertical da Imagem'))
                            ->helperText(__('Define como a imagem se alinha verticalmente em relação ao texto.'))
                            ->options([
                                'start' => 'Topo',
                                'center' => 'Meio',
                                'end' => 'Base'
                            ])
                            ->icons([
                                'start' => 'heroicon-o-bars-arrow-up',
                                'center' => 'heroicon-o-bars-2',
                                'end' => 'heroicon-o-bars-arrow-down',
                            ])
                            ->default('center')
                            ->inline(),
                    ])->collapsible()->collapsed(),
                ]),
            ]));
    }

    protected static function getDataTableBlock(): Forms\Components\Builder\Block
    {
        return Forms\Components\Builder\Block::make('data_table')
            ->preview('filament.block-previews.data_table')
            ->label(__('Tabela / Dados'))
            ->icon('heroicon-o-table-cells')
            ->schema(self::getBlockTabs([
                Forms\Components\TextInput::make('title')->label(__('Título da Tabela')),
                Forms\Components\TextInput::make('badge')->label(__('Badge superior')),
                Forms\Components\Repeater::make('rows')
                    ->label(__('Linhas de Dados'))
                    ->schema([
                        Forms\Components\TextInput::make('col1')->label(__('Projeto (Coluna 1)'))->required(),
                        Forms\Components\TextInput::make('col2')->label(__('Local (Coluna 2)'))->required(),
                        Forms\Components\Select::make('col3')->label(__('Status (Coluna 3)'))
                            ->options([
                                'success' => 'Concluído',
                                'warning' => 'Em andamento',
                                'danger' => 'Atrasado',
                                'info' => 'Planejamento',
                            ])->default('success'),
                        Forms\Components\TextInput::make('col4')->label(__('Prazo (Coluna 4)'))->required(),
                    ])
                    ->columns(4)
                    ->cloneable()
                    ->collapsible(),
            ]));
    }

    protected static function getCardsBlock(): Forms\Components\Builder\Block
    {
        return Forms\Components\Builder\Block::make('cards')
            ->preview('filament.block-previews.cards')
            ->label(__('Cards de Conteúdo'))
            ->icon('heroicon-o-square-3-stack-3d')
            ->schema(self::getBlockTabs([
                Forms\Components\TextInput::make('badge')->label(__('Badge / Subtítulo'))->helperText(__('Ex: NOSSOS PILARES')),
                Forms\Components\TextInput::make('title')->label(__('Título'))->helperText(__('Ex: O que nos move')),
                Forms\Components\Textarea::make('description')->label(__('Descrição'))->rows(3),
                Forms\Components\ToggleButtons::make('columns')
                    ->label(__('Colunas'))
                    ->options([
                        '2' => '2 Colunas',
                        '3' => '3 Colunas',
                        '4' => '4 Colunas',
                    ])
                    ->inline()
                    ->default('3'),
                Forms\Components\Repeater::make('items')
                    ->label(__('Cards'))
                    ->schema([
                        Forms\Components\TextInput::make('icon')->label(__('Ícone (Lucide)'))->default('check-circle'),
                        Forms\Components\TextInput::make('title')->label(__('Título'))->required(),
                        Forms\Components\Textarea::make('description')->label(__('Descrição'))->rows(3)->required(),
                    ])->columns(2)->defaultItems(3)->cloneable()->collapsible(),
            ]));
    }

    protected static function getFeaturedTestimonialBlock(): Forms\Components\Builder\Block
    {
        return Forms\Components\Builder\Block::make('featured_testimonial')
            ->preview('filament.block-previews.featured_testimonial')
            ->label(__('Depoimento em Destaque'))
            ->icon('heroicon-o-chat-bubble-bottom-center-text')
            ->schema(self::getBlockTabs([
                Forms\Components\Textarea::make('quote')->label(__('Depoimento (Citação)'))->required()->rows(3),
                Forms\Components\Grid::make(2)->schema([
                    Forms\Components\TextInput::make('author')->label(__('Nome do Autor'))->required(),
                    Forms\Components\TextInput::make('role')->label(__('Cargo / Empresa')),
                    Forms\Components\FileUpload::make('author_image')->label(__('Foto do Autor'))->image()->avatar()->directory('testimonials'),
                    Forms\Components\FileUpload::make('background_image')->label(__('Imagem de Fundo (Opcional)'))->image()->directory('blocks'),
                ]),
            ]));
    }

    protected static function getMapBlock(): Forms\Components\Builder\Block
    {
        return Forms\Components\Builder\Block::make('map')
            ->preview('filament.block-previews.map')
            ->label(__('Mapa (Localização)'))
            ->icon('heroicon-o-map')
            ->schema(self::getBlockTabs([
                Forms\Components\TextInput::make('title')->label(__('Título'))->helperText(__('Título do mapa.')),
                Forms\Components\Textarea::make('iframe_code')
                    ->label(__('Código de Incorporação (iframe)'))
                    ->helperText(__('Cole aqui o <iframe> gerado pelo Google Maps.')),
            ]));
    }

    protected static function getRichTextBlock(): Forms\Components\Builder\Block
    {
        return Forms\Components\Builder\Block::make('rich_text')
            ->preview('filament.block-previews.rich_text')
            ->label(__('Texto Livre (Editor)'))
            ->icon('heroicon-o-document-text')
            ->schema(self::getBlockTabs([
                Forms\Components\RichEditor::make('content')->label(__('Conteúdo'))->helperText(__('Digite o conteúdo livremente usando o editor.'))->required(),
            ]));
    }

    protected static function getContactBannerBlock(): Forms\Components\Builder\Block
    {
        return Forms\Components\Builder\Block::make('contact_banner')
            ->preview('filament.block-previews.contact_banner')
            ->label(__('Banner de Atendimento / Contato'))
            ->icon('heroicon-o-chat-bubble-left-right')
            ->schema(self::getBlockTabs([
                Forms\Components\TextInput::make('badge')
                    ->label(__('Badge (Texto Superior)'))
                    ->helperText(__('Pequeno texto de destaque.'))
                    ->default('ATENDIMENTO'),
                Forms\Components\TextInput::make('title')
                    ->label(__('Título'))
                    ->helperText(__('Título do banner de contato.'))
                    ->default('Fale conosco')
                    ->required(),
                Forms\Components\Textarea::make('description')
                    ->label(__('Descrição'))
                    ->helperText(__('Texto explicativo do banner.'))
                    ->default('Dúvidas, orçamento ou suporte: estamos prontos para atender você por telefone, WhatsApp ou e-mail.')
                    ->rows(2),
                Forms\Components\Grid::make(3)
                    ->schema([
                        Forms\Components\Toggle::make('whatsapp_enabled')
                            ->label(__('Botão WhatsApp'))
                            ->helperText(__('Exibir botão do WhatsApp.'))
                            ->onIcon('heroicon-m-check')
                            ->default(true),
                        Forms\Components\Toggle::make('call_enabled')
                            ->label(__('Botão Ligar'))
                            ->helperText(__('Exibir botão de ligação.'))
                            ->onIcon('heroicon-m-check')
                            ->default(true),
                        Forms\Components\Toggle::make('email_enabled')
                            ->label(__('Botão E-mail'))
                            ->helperText(__('Exibir botão de envio de e-mail.'))
                            ->onIcon('heroicon-m-check')
                            ->default(true),
                    ]),
            ]));
    }

    protected static function getPaymentOfferBlock(): Forms\Components\Builder\Block
    {
        return Forms\Components\Builder\Block::make('payment_offer')
            ->preview('filament.block-previews.payment_offer')
            ->label(__('Tabela de Preços / Planos'))
            ->icon('heroicon-o-credit-card')
            ->schema(self::getBlockTabs([
                Forms\Components\Grid::make(2)->schema([
                    Forms\Components\TextInput::make('badge')->label(__('Pré-título'))->default('APROVEITE ESSA MEGA OPORTUNIDADE'),
                    Forms\Components\TextInput::make('title')->label(__('Título'))->default('Parcelamento em até 12x sem juros')->required(),
                    Forms\Components\Textarea::make('subtitle')->label(__('Subtítulo'))->default('ou com desconto no pagamento à vista em dinheiro ou com o pix.')->columnSpanFull(),
                    Forms\Components\TextInput::make('button_label')->label(__('Texto do Botão'))->default('Fazer orçamento grátis'),
                    Forms\Components\TextInput::make('button_url')->label(__('URL do Botão (Deixe vazio p/ usar o WhatsApp)'))->helperText('Se vazio, enviará para o WhatsApp padrão.'),
                    Forms\Components\FileUpload::make('background_image')->image()->directory('offers')->label(__('Imagem de Fundo (Opcional)'))->columnSpanFull(),
                    Forms\Components\FileUpload::make('payment_methods_image')->image()->directory('offers')->label(__('Banner dos Meios de Pagamento (Cartões)'))->helperText('Recomendado imagem com fundo transparente (PNG/SVG) com as bandeiras dos cartões.')->columnSpanFull(),
                ])
            ]));
    }

    protected static function getModuleReferenceBlock(): Forms\Components\Builder\Block
    {
        return Forms\Components\Builder\Block::make('module_reference')
            ->preview('filament.block-previews.module_reference')
            ->label(function (?array $state): string {
                if ($state === null) {
                    return __('Módulo Global (Seção Pronta)');
                }
                $sectionName = \App\Models\ContentSection::find($state['content_section_id'] ?? null)?->name;
                return $sectionName ? __('Seção pronta - ') . $sectionName : __('Módulo Global (Seção Pronta)');
            })
            ->icon('heroicon-o-squares-plus')
            ->schema(self::getBlockTabs([
                Forms\Components\ToggleButtons::make('content_section_id')
                    ->label(__('Seção de Conteúdo'))
                    ->options(\App\Models\ContentSection::query()->pluck('name', 'id'))
                    ->required()
                    ->inline()
                    ->live()
                    ->helperText(__('Selecione uma seção criada no módulo "Seções do site" para reutilizá-la aqui.')),
            ]));
    }
}
