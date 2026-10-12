<?php

namespace App\Filament\Resources;

use App\Enums\AccessArea;
use App\Filament\Concerns\BelongsToArea;
use App\Filament\Forms\Components\SectionBuilder;
use App\Filament\Forms\Components\SectionTypePicker;
use App\Filament\Resources\ContentSectionResource\Pages;
use App\Models\ContentSection;
use App\Models\OperationArea;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;
use Illuminate\Validation\ValidationException;

class ContentSectionResource extends Resource
{
    use BelongsToArea;

    public static function getAccessArea(): AccessArea
    {
        return AccessArea::Website;
    }

    protected static ?string $model = ContentSection::class;

    protected static ?string $navigationGroup = 'Website';

    protected static ?int $navigationSort = 2;

    protected static ?string $navigationIcon = 'heroicon-o-squares-2x2';

    protected static ?string $navigationLabel = 'Seções do site';

    protected static ?string $modelLabel = 'Seção';

    protected static ?string $pluralModelLabel = 'Seções';

    /**
     * Construtor de seções: biblioteca de componentes | live preview | inspetor (wizard).
     * Os campos de conteúdo de cada tipo ficam em get*Section(), visíveis só para o tipo escolhido.
     */
    public static function form(Form $form): Form
    {
        $livewire = $form->getLivewire();

        return $form
            ->schema([
                SectionBuilder::make([
                    static::getTypePicker(),
                    Forms\Components\View::make('filament.section-builder.canvas')
                        ->viewData([
                            'types' => ContentSection::typeCatalog(),
                            'typeStatePath' => $form->getStatePath().'.type',
                        ]),
                    Forms\Components\Wizard::make([
                        static::getComponentStep(),
                        static::getContentStep(),
                        static::getAppearanceStep(),
                    ])
                        ->view('filament.section-builder.wizard')
                        // Criação: cada etapa é validada antes de avançar. Edição: navegação livre
                        ->skippable(fn (string $operation): bool => $operation === 'edit')
                        ->cancelAction(method_exists($livewire, 'getSectionBuilderCancelAction') ? $livewire->getSectionBuilderCancelAction() : null)
                        ->submitAction(method_exists($livewire, 'getSectionBuilderSubmitAction') ? $livewire->getSectionBuilderSubmitAction() : null),
                ])->columnSpanFull(),
            ])
            ->columns(1);
    }

    protected static function getTypePicker(): SectionTypePicker
    {
        return SectionTypePicker::make('type')
            ->hiddenLabel()
            ->label('Tipo da seção')
            ->types(fn (): array => ContentSection::typeCatalog())
            ->categories(ContentSection::typeCategories())
            ->usage(fn (string $operation): array => $operation === 'create'
                ? ContentSection::query()->toBase()->selectRaw('type, count(*) as total')->groupBy('type')->pluck('total', 'type')->all()
                : [])
            ->in(array_keys(ContentSection::typeLabels()))
            ->required()
            ->validationMessages(['required' => 'Selecione um componente na biblioteca para continuar.'])
            ->disabled(fn (string $operation): bool => $operation === 'edit')
            ->live()
            ->afterStateUpdated(function (Forms\Set $set, ?string $state) {
                if ($state) {
                    $set('slug', ContentSection::slugForType($state));
                    $set('name', ContentSection::typeLabels()[$state] ?? $state);
                }
            });
    }

    // ───────── Etapa 1: Componente ─────────

    protected static function getComponentStep(): Forms\Components\Wizard\Step
    {
        return Forms\Components\Wizard\Step::make('Componente')
            ->icon('heroicon-o-squares-2x2')
            ->description('Escolha o tipo de seção')
            // O tipo fica na biblioteca (fora da etapa), então é conferido aqui antes de avançar
            ->afterValidation(function (Forms\Get $get) {
                if (blank($get('type'))) {
                    throw ValidationException::withMessages([
                        'data.type' => 'Selecione um componente na biblioteca para continuar.',
                    ]);
                }
            })
            ->schema([
                Forms\Components\Placeholder::make('component_summary')
                    ->hiddenLabel()
                    ->content(function (Forms\Get $get, string $operation): HtmlString {
                        $type = $get('type');
                        $meta = ContentSection::typeCatalog()[$type] ?? null;
                        $categories = ContentSection::typeCategories();

                        return new HtmlString(view('filament.section-builder.component-summary', [
                            'type' => $type,
                            'meta' => $meta,
                            'categories' => collect($meta['categories'] ?? [])->map(fn (string $key) => $categories[$key]['label'] ?? $key)->all(),
                            'locked' => $operation === 'edit',
                        ])->render());
                    }),
                Forms\Components\Section::make('Identificação')
                    ->description('Gerados a partir do componente. Ajuste se precisar.')
                    ->icon('heroicon-o-identification')
                    ->compact()
                    ->visible(fn (Forms\Get $get): bool => filled($get('type')))
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nome (admin)')
                            ->prefixIcon('heroicon-m-tag')
                            ->required()
                            ->maxLength(255)
                            ->helperText('Nome para identificação interna na lista de seções.'),
                        Forms\Components\TextInput::make('slug')
                            ->label('Slug (identificador único)')
                            ->prefixIcon('heroicon-m-hashtag')
                            ->required()
                            ->disabled(fn (string $operation): bool => $operation === 'edit')
                            ->unique(ignoreRecord: true)
                            ->maxLength(255)
                            ->helperText('Identificador usado pelo sistema. Não editável após a criação.'),
                    ]),
            ]);
    }

    // ───────── Etapa 2: Conteúdo ─────────

    protected static function getContentStep(): Forms\Components\Wizard\Step
    {
        return Forms\Components\Wizard\Step::make('Conteúdo')
            ->icon('heroicon-o-document-text')
            ->description('Preencha os dados')
            ->schema([
                static::getHeaderSection(),
                ...static::getHeroSections(),
                static::getFaqSection(),
                ...static::getPartnersSections(),
                static::getCommercialPartnersSection(),
                static::getServicesSection(),
                static::getTestimonialsSection(),
                static::getTeamSection(),
                static::getCoverageSection(),
                static::getDifferentialsSection(),
                static::getTimelineSection(),
                static::getContactSection(),
                static::getContactBannerSection(),
                static::getInfoSection(ContentSection::TYPE_BUDGET_FORM, 'Formulário de Orçamento', 'heroicon-o-calculator', 'O formulário de orçamento (passo a passo) é exibido automaticamente. Use o "Cabeçalho" acima para definir título e descrição; a aparência vem da etapa "Aparência".'),
                static::getInfoSection(ContentSection::TYPE_CALCULATOR, 'Calculadora de Volume', 'heroicon-o-variable', 'A calculadora de volume de concreto é exibida automaticamente. Use o "Cabeçalho" acima para definir título e descrição; a aparência vem da etapa "Aparência".'),
                static::getInfoSection(ContentSection::TYPE_SHOWCASE, 'Galeria de Obras', 'heroicon-o-camera', 'As obras cadastradas em "Nossas Obras" são carregadas automaticamente. Use o "Cabeçalho" acima para definir título e descrição.'),
                static::getInfoSection(ContentSection::TYPE_EQUIPMENT_SHOWCASE, 'Showcase de Equipamentos', 'heroicon-o-truck', 'Os equipamentos cadastrados em "Equipamentos" são carregados automaticamente, com busca e filtros. Use o "Cabeçalho" acima para definir título (ex.: "Equipamentos para cada etapa da sua obra") e descrição.'),
                static::getPaymentOfferSection(),
                static::getSimpleBannerSection(),
            ]);
    }

    /**
     * Seção do inspetor visível apenas para um tipo.
     */
    protected static function typeSection(string $type, string $heading, string $icon, ?string $description = null): Forms\Components\Section
    {
        return Forms\Components\Section::make($heading)
            ->description($description)
            ->icon($icon)
            ->compact()
            ->collapsible()
            ->visible(fn (Forms\Get $get): bool => $get('type') === $type);
    }

    protected static function getInfoSection(string $type, string $heading, string $icon, string $text): Forms\Components\Section
    {
        return static::typeSection($type, $heading, $icon)
            ->schema([
                Forms\Components\Placeholder::make('info_'.$type)
                    ->hiddenLabel()
                    ->content($text),
            ]);
    }

    protected static function getHeaderSection(): Forms\Components\Section
    {
        return Forms\Components\Section::make('Cabeçalho (opcional)')
            ->icon('heroicon-o-bars-3-bottom-left')
            ->description('Pré-título, título e descrição exibidos no topo da seção.')
            ->compact()
            ->collapsible()
            ->schema([
                Forms\Components\Toggle::make('content.header.visible')
                    ->label('Exibir cabeçalho')
                    ->onIcon('heroicon-m-check')->offIcon('heroicon-m-x-mark')
                    ->helperText('Liga ou desliga o cabeçalho.')
                    ->default(true),
                Forms\Components\ToggleButtons::make('content.header.alignment')
                    ->label('Alinhamento')
                    ->helperText('Posição do texto.')
                    ->options([
                        'left' => 'Esquerda',
                        'center' => 'Centro',
                        'right' => 'Direita',
                    ])
                    ->icons([
                        'left' => 'heroicon-m-bars-3-bottom-left',
                        'center' => 'heroicon-m-bars-3',
                        'right' => 'heroicon-m-bars-3-bottom-right',
                    ])
                    ->inline()
                    ->grouped()
                    ->default('center'),
                Forms\Components\TextInput::make('content.header.subtitle')
                    ->label('Pré-título')
                    ->prefixIcon('heroicon-m-bookmark')
                    ->helperText('Aparece em destaque acima do título.')
                    ->maxLength(255),
                Forms\Components\TextInput::make('content.header.title')
                    ->label('Título')
                    ->prefixIcon('heroicon-m-h1')
                    ->helperText('Título principal da seção.')
                    ->maxLength(255),
                Forms\Components\Textarea::make('content.header.description')
                    ->label('Descrição')
                    ->helperText('Texto explicativo abaixo do título principal.')
                    ->rows(2)
                    ->autosize(),
            ]);
    }

    /**
     * @return array<Forms\Components\Section>
     */
    protected static function getHeroSections(): array
    {
        $type = ContentSection::TYPE_HERO;

        $iconOptions = [
            'arrow-right' => 'Seta',
            'phone' => 'Telefone',
            'message-circle' => 'WhatsApp',
            'calculator' => 'Calculadora',
            'outro' => 'Outro',
        ];
        $iconIcons = [
            'arrow-right' => 'heroicon-o-arrow-right',
            'phone' => 'heroicon-o-phone',
            'message-circle' => 'heroicon-o-chat-bubble-oval-left-ellipsis',
            'calculator' => 'heroicon-o-calculator',
            'outro' => 'heroicon-o-magnifying-glass',
        ];

        $button = fn (string $prefix, string $heading, string $textHelp, string $urlExample, string $iconExample): Forms\Components\Fieldset => Forms\Components\Fieldset::make($heading)
            ->columns(1)
            ->schema([
                Forms\Components\TextInput::make("content.{$prefix}_button_text")
                    ->label('Texto do botão')
                    ->prefixIcon('heroicon-m-cursor-arrow-rays')
                    ->helperText($textHelp),
                Forms\Components\TextInput::make("content.{$prefix}_button_url")
                    ->label('Link')
                    ->prefixIcon('heroicon-m-link')
                    ->helperText("Para onde o botão leva (ex: {$urlExample})."),
                Forms\Components\ToggleButtons::make("content.{$prefix}_button_icon_select")
                    ->label('Ícone')
                    ->helperText('Escolha um ícone pronto ou defina um personalizado.')
                    ->options($iconOptions)
                    ->icons($iconIcons)
                    ->inline()
                    ->live(),
                Forms\Components\TextInput::make("content.{$prefix}_button_icon")
                    ->label('Nome do ícone')
                    ->prefixIcon('heroicon-m-sparkles')
                    ->placeholder("ex: {$iconExample}")
                    ->helperText('Busque o nome do ícone em lucide.dev/icons.')
                    ->visible(fn (Forms\Get $get) => $get("content.{$prefix}_button_icon_select") === 'outro'),
            ]);

        return [
            static::typeSection($type, 'Layout e mídia', 'heroicon-o-photo', 'Estilo do banner e mídia de fundo.')
                ->schema([
                    Forms\Components\ToggleButtons::make('content.layout')
                        ->label('Layout')
                        ->helperText('Estilo de exibição da seção principal.')
                        ->options([
                            'default' => 'Busca por CEP',
                            'whatsapp' => 'Contato via WhatsApp',
                            'clean' => 'Institucional (Minimalista)',
                        ])
                        ->icons([
                            'default' => 'heroicon-o-map-pin',
                            'whatsapp' => 'heroicon-o-chat-bubble-left-right',
                            'clean' => 'heroicon-o-stop',
                        ])
                        ->default('default')
                        ->inline(),
                    Forms\Components\FileUpload::make('content.image')
                        ->label('Imagem de fundo')
                        ->helperText('Imagem principal no fundo da seção. Recomendado: 1920 × 1080 px (JPG ou PNG).')
                        ->image()
                        ->imagePreviewHeight('140')
                        ->directory('hero'),
                    Forms\Components\FileUpload::make('content.video')
                        ->label('Vídeo de fundo (.mp4)')
                        ->helperText('Se enviado, substitui a imagem de fundo. Recomendado até 15 MB.')
                        ->acceptedFileTypes(['video/mp4'])
                        ->directory('hero'),
                    Forms\Components\ToggleButtons::make('content.image_alignment')
                        ->label('Alinhamento da imagem / vídeo')
                        ->helperText('Foco visual da mídia de fundo.')
                        ->options([
                            'center' => 'Centro',
                            'top' => 'Topo',
                            'bottom' => 'Base',
                        ])
                        ->icons([
                            'center' => 'heroicon-o-arrows-pointing-in',
                            'top' => 'heroicon-o-arrow-up',
                            'bottom' => 'heroicon-o-arrow-down',
                        ])
                        ->default('center')
                        ->inline()
                        ->grouped(),
                    Forms\Components\Toggle::make('content.overlay_enabled')
                        ->label('Ativar overlay')
                        ->onIcon('heroicon-m-check')->offIcon('heroicon-m-x-mark')
                        ->helperText('Camada sobre a mídia para melhorar a leitura.')
                        ->default(true)
                        ->live(),
                    Forms\Components\ToggleButtons::make('content.overlay_theme')
                        ->label('Tema do overlay')
                        ->helperText('Película escura ou clara.')
                        ->options([
                            'dark' => 'Escuro',
                            'light' => 'Claro',
                        ])
                        ->icons([
                            'dark' => 'heroicon-o-moon',
                            'light' => 'heroicon-o-sun',
                        ])
                        ->default('dark')
                        ->inline()
                        ->grouped()
                        ->visible(fn (Forms\Get $get) => $get('content.overlay_enabled')),
                ]),

            static::typeSection($type, 'Slideshow', 'heroicon-o-rectangle-stack', 'Carrossel de imagens no fundo, no lugar da mídia fixa.')
                ->collapsed()
                ->schema([
                    Forms\Components\Toggle::make('content.show_slideshow')
                        ->onIcon('heroicon-m-check')->offIcon('heroicon-m-x-mark')
                        ->label('Habilitar slideshow')
                        ->helperText('Exibe um carrossel rotativo atrás do banner em vez da imagem/vídeo fixo.')
                        ->default(false)
                        ->live(),
                    Forms\Components\Repeater::make('content.slideshow')
                        ->label('Slides de fundo')
                        ->helperText('Até 3 imagens, exibidas em sequência.')
                        ->schema([
                            Forms\Components\FileUpload::make('image')
                                ->label('Imagem')
                                ->helperText('Imagem do slide. Recomendado: 1920 × 1080 px.')
                                ->image()
                                ->imagePreviewHeight('120')
                                ->directory('hero')
                                ->required(),
                            Forms\Components\ToggleButtons::make('image_alignment')
                                ->label('Alinhamento da imagem')
                                ->helperText('Qual parte da imagem fica em foco.')
                                ->options([
                                    'center' => 'Centro',
                                    'top' => 'Topo',
                                    'bottom' => 'Base',
                                    'left' => 'Esq.',
                                    'right' => 'Dir.',
                                ])
                                ->icons([
                                    'center' => 'heroicon-o-arrows-pointing-in',
                                    'top' => 'heroicon-o-arrow-up',
                                    'bottom' => 'heroicon-o-arrow-down',
                                    'left' => 'heroicon-o-arrow-left',
                                    'right' => 'heroicon-o-arrow-right',
                                ])
                                ->default('center')
                                ->inline(),
                        ])
                        ->collapsible()
                        ->cloneable()
                        ->maxItems(3)
                        ->itemLabel(function (array $state): string {
                            $image = $state['image'] ?? null;
                            if (is_array($image)) {
                                $image = array_values($image)[0] ?? null;
                            }

                            return $image ? 'Imagem: '.basename((string) $image) : 'Novo Slide';
                        })
                        ->addActionLabel('Adicionar imagem')
                        ->visible(fn (Forms\Get $get) => $get('content.show_slideshow')),
                ]),

            static::typeSection($type, 'Botões de ação', 'heroicon-o-cursor-arrow-rays', 'Chamadas principais do banner.')
                ->schema([
                    Forms\Components\Toggle::make('content.show_action_buttons')
                        ->onIcon('heroicon-m-check')->offIcon('heroicon-m-x-mark')
                        ->label('Exibir botões de ação')
                        ->helperText('Mostra os botões no banner.')
                        ->default(true)
                        ->live(),
                    Forms\Components\Group::make([
                        $button('primary', 'Botão principal', 'Cor principal (fundo sólido).', '/#contato', 'arrow-right'),
                        $button('secondary', 'Botão secundário', 'Fundo transparente com borda.', '/#servicos', 'phone'),
                    ])->visible(fn (Forms\Get $get) => $get('content.show_action_buttons') === true),
                ]),

            static::typeSection($type, 'Estatísticas', 'heroicon-o-chart-bar', 'Números em destaque no rodapé do banner.')
                ->collapsed()
                ->schema([
                    Forms\Components\Toggle::make('content.show_stats')
                        ->onIcon('heroicon-m-check')->offIcon('heroicon-m-x-mark')
                        ->label('Exibir estatísticas')
                        ->helperText('Mostra o bloco de números no banner.')
                        ->default(true),
                    Forms\Components\Repeater::make('content.stats')
                        ->label('Estatísticas (máx. 3)')
                        ->helperText('Números importantes em destaque (ex: +500 Projetos).')
                        ->schema([
                            Forms\Components\TextInput::make('value')->label('Valor')->prefixIcon('heroicon-m-arrow-trending-up')->helperText('Ex: +500')->required(),
                            Forms\Components\TextInput::make('label')->label('Rótulo')->prefixIcon('heroicon-m-tag')->helperText('Ex: Projetos entregues')->required(),
                        ])
                        ->columns(2)
                        ->collapsible()
                        ->cloneable()
                        ->maxItems(3)
                        ->itemLabel(fn (array $state): ?string => trim(($state['value'] ?? '').' '.($state['label'] ?? '')) ?: null),
                ]),
        ];
    }

    protected static function getFaqSection(): Forms\Components\Section
    {
        return static::typeSection(ContentSection::TYPE_FAQ, 'Perguntas e respostas', 'heroicon-o-question-mark-circle', 'Perguntas frequentes exibidas em acordeão.')
            ->schema([
                Forms\Components\Repeater::make('content.items')
                    ->label('Itens do FAQ')
                    ->helperText('Arraste para reordenar. A primeira pergunta abre expandida.')
                    ->schema([
                        Forms\Components\TextInput::make('question')->label('Pergunta')->prefixIcon('heroicon-m-question-mark-circle')->helperText('A dúvida do cliente.')->required(),
                        Forms\Components\Textarea::make('answer')->label('Resposta')->helperText('A resposta para a dúvida.')->required()->rows(2)->autosize(),
                    ])
                    ->columns(1)
                    ->collapsible()
                    ->addActionLabel('Adicionar pergunta')
                    ->itemLabel(fn (array $state): ?string => $state['question'] ?? null),
            ]);
    }

    /**
     * @return array<Forms\Components\Section>
     */
    protected static function getPartnersSections(): array
    {
        $type = ContentSection::TYPE_PARTNERS;

        return [
            static::typeSection($type, 'Design e layout', 'heroicon-o-paint-brush', 'Como os logotipos são exibidos.')
                ->schema([
                    Forms\Components\ToggleButtons::make('content.layout')
                        ->label('Layout')
                        ->helperText('Carrossel contínuo ou logotipos lado a lado.')
                        ->options([
                            'slider' => 'Slider (Carrossel Contínuo)',
                            'grid' => 'Grid (Lado a Lado)',
                        ])
                        ->icons([
                            'slider' => 'heroicon-o-arrows-right-left',
                            'grid' => 'heroicon-o-squares-2x2',
                        ])
                        ->default('slider')
                        ->inline(),
                ]),
            static::typeSection($type, 'Parceiros', 'heroicon-o-building-office-2', 'Logomarcas ou nomes das empresas.')
                ->schema([
                    Forms\Components\Repeater::make('content.items')
                        ->label('Empresas / parceiros')
                        ->helperText('Cada item exibe o logo; sem logo, mostra o ícone e o nome.')
                        ->schema([
                            Forms\Components\FileUpload::make('logo')
                                ->label('Logo da empresa')
                                ->helperText('PNG ou SVG com fundo transparente fica melhor.')
                                ->image()
                                ->imagePreviewHeight('80')
                                ->directory('partners'),
                            Forms\Components\TextInput::make('name')->label('Nome')->prefixIcon('heroicon-m-building-office')->helperText('Nome do parceiro.')->required(),
                            Forms\Components\TextInput::make('icon')
                                ->label('Ícone (Lucide)')
                                ->prefixIcon('heroicon-m-sparkles')
                                ->helperText('Usado quando não há logo.')
                                ->placeholder('building-2, hard-hat, factory...')
                                ->maxLength(50),
                        ])
                        ->columns(1)
                        ->collapsible()
                        ->addActionLabel('Adicionar parceiro')
                        ->itemLabel(fn (array $state): ?string => $state['name'] ?? null),
                ]),
        ];
    }

    protected static function getCommercialPartnersSection(): Forms\Components\Section
    {
        return static::typeSection(ContentSection::TYPE_COMMERCIAL_PARTNERS, 'Parceiros comerciais (automático)', 'heroicon-o-building-office', 'Exibe os parceiros cadastrados em "Empresa > Parceiros". Personalize a imagem e os tópicos.')
            ->schema([
                Forms\Components\FileUpload::make('content.main_image')
                    ->label('Imagem principal (direita)')
                    ->helperText('Imagem ao lado do texto (ex: aperto de mãos).')
                    ->image()
                    ->imagePreviewHeight('140')
                    ->directory('sections'),
                Forms\Components\Repeater::make('content.features')
                    ->label('Tópicos de destaque')
                    ->helperText('Até 4 tópicos curtos com ícone.')
                    ->schema([
                        Forms\Components\TextInput::make('title')->label('Título')->prefixIcon('heroicon-m-pencil')->helperText('Texto do tópico.')->required(),
                        Forms\Components\TextInput::make('icon')
                            ->label('Ícone (Lucide)')
                            ->prefixIcon('heroicon-m-sparkles')
                            ->helperText('Ex: check-circle, handshake.')
                            ->default('check-circle')
                            ->required(),
                    ])
                    ->columns(2)
                    ->maxItems(4)
                    ->addActionLabel('Adicionar tópico')
                    ->itemLabel(fn (array $state): ?string => $state['title'] ?? null),
            ]);
    }

    protected static function getServicesSection(): Forms\Components\Section
    {
        return static::typeSection(ContentSection::TYPE_SERVICES, 'Serviços', 'heroicon-o-wrench-screwdriver', 'Serviços oferecidos e seus detalhes.')
            ->schema([
                Forms\Components\Repeater::make('content.items')
                    ->label('Serviços')
                    ->helperText('Cada serviço vira um card. Arraste para reordenar.')
                    ->schema([
                        Forms\Components\TextInput::make('title')->label('Título')->prefixIcon('heroicon-m-pencil')->helperText('Nome do serviço.')->required(),
                        Forms\Components\TextInput::make('subtitle')->label('Subtítulo (ex: por m³)')->prefixIcon('heroicon-m-bars-3-bottom-left')->helperText('Informação extra rápida.'),
                        Forms\Components\Textarea::make('description')->label('Descrição')->helperText('Detalhes do serviço.')->rows(2)->autosize(),
                        Forms\Components\ToggleButtons::make('_icon_preset')
                            ->label('Ícone (Lucide)')
                            ->helperText('Escolha um ícone pronto ou "Outro" para digitar o nome.')
                            ->options([
                                'droplets' => 'Gotas',
                                'gauge' => 'Medidor',
                                'wrench' => 'Ferramenta',
                                'check-circle' => 'Check',
                                'star' => 'Estrela',
                                'outro' => 'Outro...',
                            ])
                            ->icons([
                                'droplets' => 'heroicon-o-beaker',
                                'gauge' => 'heroicon-o-clock',
                                'wrench' => 'heroicon-o-wrench-screwdriver',
                                'check-circle' => 'heroicon-o-check-circle',
                                'star' => 'heroicon-o-star',
                                'outro' => 'heroicon-o-plus',
                            ])
                            ->inline()
                            ->live()
                            ->afterStateHydrated(function (Forms\Set $set, Forms\Get $get) {
                                $icon = $get('icon');
                                if (in_array($icon, ['droplets', 'gauge', 'wrench', 'check-circle', 'star'])) {
                                    $set('_icon_preset', $icon);
                                } elseif ($icon) {
                                    $set('_icon_preset', 'outro');
                                }
                            })
                            ->afterStateUpdated(function ($state, Forms\Set $set) {
                                if ($state !== 'outro') {
                                    $set('icon', $state);
                                } else {
                                    $set('icon', null);
                                }
                            })
                            ->dehydrated(false),
                        Forms\Components\TextInput::make('icon')
                            ->label('Nome do ícone Lucide')
                            ->prefixIcon('heroicon-m-sparkles')
                            ->helperText('Digite o nome do ícone. Ex: camera, car.')
                            ->hidden(fn (Forms\Get $get) => $get('_icon_preset') !== 'outro')
                            ->required(fn (Forms\Get $get) => $get('_icon_preset') === 'outro')
                            ->dehydrated(true),
                        Forms\Components\TagsInput::make('bullets')->label('Lista de itens')->prefixIcon('heroicon-m-list-bullet')->helperText('Tópicos do serviço. Pressione Enter para adicionar.')->placeholder('Item'),
                        Forms\Components\Grid::make(2)->schema([
                            Forms\Components\TextInput::make('cta_label')->label('Texto do link')->prefixIcon('heroicon-m-cursor-arrow-rays')->helperText('Texto do botão.')->default('Solicitar Orçamento'),
                            Forms\Components\TextInput::make('cta_url')->label('URL do link')->prefixIcon('heroicon-m-link')->helperText('Para onde o botão leva.')->default('#orcamento'),
                        ]),
                    ])
                    ->cloneable()
                    ->collapsible()
                    ->collapsed()
                    ->addActionLabel('Adicionar serviço')
                    ->itemLabel(fn (array $state): ?string => $state['title'] ?? null),
            ]);
    }

    protected static function getTestimonialsSection(): Forms\Components\Section
    {
        return static::typeSection(ContentSection::TYPE_TESTIMONIALS, 'Depoimentos', 'heroicon-o-chat-bubble-left-right', 'Depoimentos e avaliações de clientes.')
            ->schema([
                Forms\Components\Repeater::make('content.items')
                    ->label('Depoimentos')
                    ->helperText('Exibidos em carrossel, na ordem da lista.')
                    ->schema([
                        Forms\Components\Textarea::make('quote')->label('Citação')->helperText('O texto do depoimento.')->required()->rows(3)->autosize(),
                        Forms\Components\TextInput::make('author_name')->label('Nome do autor')->prefixIcon('heroicon-m-user')->helperText('Pessoa que deu o depoimento.')->required(),
                        Forms\Components\TextInput::make('author_role')->label('Cargo / obra')->prefixIcon('heroicon-m-briefcase')->helperText('Ex: Cliente Codhous.'),
                        Forms\Components\TextInput::make('stars')->label('Estrelas (1-5)')->prefixIcon('heroicon-m-star')->helperText('Nota de 1 a 5.')->numeric()->minValue(1)->maxValue(5)->default(5),
                        Forms\Components\FileUpload::make('avatar')->label('Foto do perfil (opcional)')->helperText('Foto quadrada do autor.')->image()->directory('testimonials/avatars')->avatar(),
                        Forms\Components\FileUpload::make('image')->label('Imagem da obra (opcional)')->helperText('Foto do trabalho realizado.')->image()->imagePreviewHeight('120')->directory('testimonials/images'),
                    ])
                    ->cloneable()
                    ->collapsible()
                    ->collapsed()
                    ->addActionLabel('Adicionar depoimento')
                    ->itemLabel(fn (array $state): ?string => $state['author_name'] ?? null),
            ]);
    }

    protected static function getTeamSection(): Forms\Components\Section
    {
        $highlight = fn (int $n, string $iconDefault, string $iconExample, string $titleExample, string $subtitleExample): Forms\Components\Fieldset => Forms\Components\Fieldset::make("Destaque {$n}")
            ->columns(1)
            ->schema([
                Forms\Components\TextInput::make("highlight_{$n}_icon")->label('Ícone (Lucide)')->prefixIcon('heroicon-m-sparkles')->default($iconDefault)->helperText("Ex: {$iconExample}"),
                Forms\Components\TextInput::make("highlight_{$n}_title")->label('Título (negrito)')->prefixIcon('heroicon-m-trophy')->helperText("Ex: {$titleExample}"),
                Forms\Components\TextInput::make("highlight_{$n}_subtitle")->label('Subtítulo')->prefixIcon('heroicon-m-bars-3-bottom-left')->helperText("Ex: {$subtitleExample}"),
            ]);

        return static::typeSection(ContentSection::TYPE_TEAM, 'Nosso time', 'heroicon-o-users', 'Membros da equipe e profissionais da empresa.')
            ->schema([
                Forms\Components\Repeater::make('content.items')
                    ->label('Membros da equipe')
                    ->helperText('Cada membro vira um card com foto, cargo e destaques.')
                    ->schema([
                        Forms\Components\FileUpload::make('avatar')
                            ->label('Foto')
                            ->image()
                            ->directory('team/avatars')
                            ->avatar()
                            ->helperText('Retangular ou quadrada. Fica no topo do card.'),
                        Forms\Components\TextInput::make('name')
                            ->label('Nome')
                            ->prefixIcon('heroicon-m-user')
                            ->helperText('Nome completo do membro.')
                            ->required(),
                        Forms\Components\TextInput::make('role')
                            ->label('Cargo (destaque)')
                            ->prefixIcon('heroicon-m-briefcase')
                            ->helperText('Cargo principal, na cor de destaque.')
                            ->required(),
                        Forms\Components\ToggleButtons::make('icon_type')
                            ->label('Ícone do cargo')
                            ->helperText('Ícone ao lado do cargo (Lucide Icons).')
                            ->options([
                                'graduation-cap' => 'Acadêmico',
                                'settings' => 'Operações',
                                'hard-hat' => 'Obras',
                                'flask-conical' => 'Química',
                                'briefcase' => 'Negócios',
                                'other' => 'Outro...',
                            ])
                            ->icons([
                                'graduation-cap' => 'heroicon-m-academic-cap',
                                'settings' => 'heroicon-m-cog-8-tooth',
                                'hard-hat' => 'heroicon-m-wrench-screwdriver',
                                'flask-conical' => 'heroicon-m-beaker',
                                'briefcase' => 'heroicon-m-briefcase',
                                'other' => 'heroicon-m-pencil',
                            ])
                            ->inline()
                            ->default('graduation-cap')
                            ->live(),
                        Forms\Components\TextInput::make('icon')
                            ->label('Nome do ícone customizado')
                            ->prefixIcon('heroicon-m-sparkles')
                            ->helperText('Veja os nomes em lucide.dev/icons (ex: "star", "zap").')
                            ->visible(fn (Forms\Get $get) => $get('icon_type') === 'other')
                            ->required(fn (Forms\Get $get) => $get('icon_type') === 'other'),
                        Forms\Components\TextInput::make('sub_role')
                            ->label('Sub-cargo (opcional)')
                            ->prefixIcon('heroicon-m-bars-3-bottom-left')
                            ->helperText('Informação extra, em cinza, abaixo do cargo.'),
                        $highlight(1, 'building-2', 'building-2', '12+ anos', 'de experiência'),
                        $highlight(2, 'users', 'users', 'Liderança', 'e estratégia'),
                    ])
                    ->cloneable()
                    ->collapsible()
                    ->collapsed()
                    ->addActionLabel('Adicionar membro')
                    ->itemLabel(fn (array $state): ?string => $state['name'] ?? null),
            ]);
    }

    protected static function getCoverageSection(): Forms\Components\Section
    {
        return static::typeSection(ContentSection::TYPE_COVERAGE, 'Onde atuamos', 'heroicon-o-map-pin', 'Cidades atendidas e cards informativos.')
            ->schema([
                Forms\Components\Select::make('content.cities')
                    ->label('Cidades atendidas')
                    ->prefixIcon('heroicon-m-map-pin')
                    ->multiple()
                    ->options(fn () => OperationArea::query()->where('is_active', true)->pluck('city', 'city'))
                    ->afterStateHydrated(function (Forms\Components\Select $component, $state) {
                        if (is_array($state)) {
                            $flat = [];
                            foreach ($state as $city) {
                                if (is_array($city)) {
                                    $flat[] = $city['label'] ?? $city['city'] ?? '';
                                } else {
                                    $flat[] = $city;
                                }
                            }
                            $component->state(array_filter($flat));
                        }
                    })
                    ->helperText('Cidades em destaque. Os dados vêm de "Áreas de Operação".'),
                Forms\Components\FileUpload::make('content.background_media')
                    ->label('Imagem ou vídeo de fundo')
                    ->helperText('Imagem ou vídeo curto (MP4) sobreposto à seção. Até 20 MB.')
                    ->directory('coverage')
                    ->acceptedFileTypes(['image/*', 'video/mp4', 'video/webm', 'video/quicktime'])
                    ->maxSize(20480),
                Forms\Components\Repeater::make('content.sidebar')
                    ->label('Cards laterais')
                    ->helperText('Informações exibidas ao lado do mapa.')
                    ->schema([
                        Forms\Components\TextInput::make('title')->label('Título')->prefixIcon('heroicon-m-pencil')->helperText('Título do card.')->required(),
                        Forms\Components\TextInput::make('icon')->label('Ícone Lucide')->prefixIcon('heroicon-m-sparkles')->helperText('Opcional. Ex: map-pin, truck.'),
                        Forms\Components\Textarea::make('description')->label('Descrição')->helperText('Texto do card.')->rows(2)->autosize(),
                    ])
                    ->cloneable()
                    ->collapsible()
                    ->collapsed()
                    ->addActionLabel('Adicionar card')
                    ->itemLabel(fn (array $state): ?string => $state['title'] ?? null),
            ]);
    }

    protected static function getDifferentialsSection(): Forms\Components\Section
    {
        return static::typeSection(ContentSection::TYPE_DIFFERENTIALS, 'Diferenciais', 'heroicon-o-star', 'Principais diferenciais ou pilares da empresa.')
            ->schema([
                Forms\Components\Repeater::make('content.items')
                    ->label('Itens')
                    ->helperText('Cada diferencial aparece com ícone, título e descrição.')
                    ->schema([
                        Forms\Components\TextInput::make('icon')->label('Ícone Lucide')->prefixIcon('heroicon-m-sparkles')->helperText('Ícone do diferencial.')->placeholder('clock, microscope'),
                        Forms\Components\TextInput::make('title')->label('Título')->prefixIcon('heroicon-m-pencil')->helperText('Nome do diferencial.')->required(),
                        Forms\Components\Textarea::make('description')->label('Descrição')->helperText('O que isso significa para o cliente.')->rows(2)->autosize(),
                    ])
                    ->columns(1)
                    ->collapsible()
                    ->addActionLabel('Adicionar diferencial')
                    ->itemLabel(fn (array $state): ?string => $state['title'] ?? null),
            ]);
    }

    protected static function getTimelineSection(): Forms\Components\Section
    {
        return static::typeSection(ContentSection::TYPE_TIMELINE, 'Etapas (como funciona)', 'heroicon-o-clock', 'Passo a passo do seu processo.')
            ->schema([
                Forms\Components\Repeater::make('content.steps')
                    ->label('Etapas')
                    ->helperText('Exibidas na ordem da lista, ligadas por uma linha.')
                    ->schema([
                        Forms\Components\TextInput::make('step_label')->label('Rótulo (ex: Etapa 1)')->prefixIcon('heroicon-m-numbered-list')->helperText('Indicador da etapa.')->required(),
                        Forms\Components\TextInput::make('title')->label('Título')->prefixIcon('heroicon-m-pencil')->helperText('Nome da etapa.')->required(),
                        Forms\Components\Textarea::make('description')->label('Descrição')->helperText('Explicação do que acontece.')->rows(2)->autosize(),
                        Forms\Components\TextInput::make('icon')->label('Ícone Lucide')->prefixIcon('heroicon-m-sparkles')->helperText('Ícone da etapa.')->placeholder('message-square-text, truck'),
                    ])
                    ->columns(1)
                    ->collapsible()
                    ->addActionLabel('Adicionar etapa')
                    ->itemLabel(fn (array $state): ?string => $state['title'] ?? null),
            ]);
    }

    protected static function getContactSection(): Forms\Components\Section
    {
        $toggle = fn (string $name, string $label, string $help): Forms\Components\Toggle => Forms\Components\Toggle::make($name)
            ->label($label)
            ->helperText($help)
            ->onIcon('heroicon-m-check')->offIcon('heroicon-m-x-mark')
            ->default(true);

        return static::typeSection(ContentSection::TYPE_CTA_CONTACT, 'Contato', 'heroicon-o-envelope', 'Textos e botões do formulário de contato.')
            ->schema([
                Forms\Components\TextInput::make('content.email_to')
                    ->label('E-mail de destino (opcional)')
                    ->prefixIcon('heroicon-m-envelope')
                    ->helperText('Se vazio, envia para o e-mail padrão da empresa.'),
                Forms\Components\Fieldset::make('Botão de orçamento')
                    ->columns(1)
                    ->schema([
                        $toggle('content.budget_btn_enabled', 'Mostrar botão', 'Liga ou desliga esse botão.'),
                        Forms\Components\TextInput::make('content.budget_btn_title')
                            ->label('Título')
                            ->prefixIcon('heroicon-m-pencil')
                            ->helperText('Texto maior do botão.')
                            ->default('Orçamento Grátis Online'),
                        Forms\Components\TextInput::make('content.budget_btn_subtitle')
                            ->label('Subtítulo')
                            ->prefixIcon('heroicon-m-bars-3-bottom-left')
                            ->helperText('Texto menor do botão.')
                            ->default('Faça uma cotação rápida agora'),
                        Forms\Components\TextInput::make('content.budget_btn_url')
                            ->label('Link do botão')
                            ->prefixIcon('heroicon-m-link')
                            ->helperText('URL ou âncora de destino. Ex: /#orcamento')
                            ->default('/#orcamento'),
                    ]),
                Forms\Components\Fieldset::make('Botões adicionais')
                    ->columns(2)
                    ->schema([
                        $toggle('content.whatsapp_btn_enabled', 'WhatsApp', 'Número das configurações do site.'),
                        $toggle('content.email_btn_enabled', 'E-mail', 'Usa o e-mail informado acima.'),
                        $toggle('content.phone_btn_enabled', 'Telefone', 'Do cadastro da empresa.'),
                        $toggle('content.address_enabled', 'Endereço', 'Do cadastro da empresa.'),
                    ]),
            ]);
    }

    protected static function getContactBannerSection(): Forms\Components\Section
    {
        $toggle = fn (string $name, string $label, string $help): Forms\Components\Toggle => Forms\Components\Toggle::make($name)
            ->label($label)
            ->helperText($help)
            ->onIcon('heroicon-m-check')->offIcon('heroicon-m-x-mark')
            ->default(true);

        return static::typeSection(ContentSection::TYPE_CONTACT_BANNER, 'Banner de atendimento', 'heroicon-o-megaphone', 'Chamada para atendimento rápido (WhatsApp, ligação, e-mail).')
            ->schema([
                Forms\Components\TextInput::make('content.badge')
                    ->label('Selo (texto superior)')
                    ->prefixIcon('heroicon-m-tag')
                    ->helperText('Exibido pequeno acima do título.')
                    ->placeholder('ATENDIMENTO')
                    ->default('ATENDIMENTO'),
                Forms\Components\TextInput::make('content.title')
                    ->label('Título')
                    ->prefixIcon('heroicon-m-h1')
                    ->helperText('Chamada principal.')
                    ->default('Fale conosco')
                    ->required(),
                Forms\Components\Textarea::make('content.description')
                    ->label('Descrição')
                    ->helperText('Texto de apoio abaixo do título.')
                    ->default('Dúvidas, orçamento ou suporte: estamos prontos para atender você por telefone, WhatsApp ou e-mail.')
                    ->rows(2)
                    ->autosize(),
                Forms\Components\Fieldset::make('Botões')
                    ->columns(3)
                    ->schema([
                        $toggle('content.whatsapp_enabled', 'WhatsApp', 'Botão do WhatsApp.'),
                        $toggle('content.call_enabled', 'Ligar', 'Botão de ligação.'),
                        $toggle('content.email_enabled', 'E-mail', 'Botão de e-mail.'),
                    ]),
            ]);
    }

    protected static function getPaymentOfferSection(): Forms\Components\Section
    {
        return static::typeSection(ContentSection::TYPE_PAYMENT_OFFER, 'Oferta de pagamento', 'heroicon-o-credit-card', 'Oferta e meios de pagamento aceitos.')
            ->schema([
                Forms\Components\TextInput::make('content.button_label')
                    ->label(__('Texto do botão'))
                    ->prefixIcon('heroicon-m-cursor-arrow-rays')
                    ->helperText('Chamada do botão principal.')
                    ->default('Fazer orçamento grátis'),
                Forms\Components\TextInput::make('content.button_url')
                    ->label(__('URL do botão'))
                    ->prefixIcon('heroicon-m-link')
                    ->helperText('Se vazio, o botão leva ao WhatsApp padrão.'),
                Forms\Components\Toggle::make('content.show_payment_methods')
                    ->label(__('Exibir meios de pagamento'))
                    ->helperText('Mostra o banner com as bandeiras aceitas.')
                    ->default(true)
                    ->live(),
                Forms\Components\FileUpload::make('content.payment_methods_image')
                    ->label(__('Banner dos meios de pagamento (cartões)'))
                    ->helperText('Imagem com fundo transparente (PNG/SVG) com as bandeiras dos cartões.')
                    ->image()
                    ->imageEditor()
                    ->imagePreviewHeight('80')
                    ->directory('sections/payment')
                    ->visible(fn (Forms\Get $get) => $get('content.show_payment_methods') !== false),
            ]);
    }

    protected static function getSimpleBannerSection(): Forms\Components\Section
    {
        return static::typeSection(ContentSection::TYPE_SIMPLE_BANNER, 'Banner simples', 'heroicon-o-photo', 'Imagem clicável para chamadas promocionais.')
            ->schema([
                Forms\Components\FileUpload::make('content.image')
                    ->label('Imagem do banner')
                    ->helperText('Exibida em largura total. Recomendado: 1920 px de largura.')
                    ->image()
                    ->imageEditor()
                    ->imagePreviewHeight('140')
                    ->directory('sections/banners')
                    ->required(),
                Forms\Components\TextInput::make('content.link_url')
                    ->label('Link de destino')
                    ->prefixIcon('heroicon-m-link')
                    ->helperText('Endereço completo, com https://.')
                    ->url()
                    ->placeholder('Ex: https://...'),
                Forms\Components\Toggle::make('content.open_in_new_tab')
                    ->label('Abrir link em nova aba')
                    ->helperText('Mantém o site aberto na aba atual.')
                    ->default(true),
            ]);
    }

    // ───────── Etapa 3: Aparência e Publicação ─────────

    protected static function getAppearanceStep(): Forms\Components\Wizard\Step
    {
        $percentages = fn (array $values): array => collect($values)->mapWithKeys(fn (int $value) => [(string) $value => "{$value}%"])->all();
        $hasImage = fn (Forms\Get $get): bool => filled($get('content.background_image'));

        return Forms\Components\Wizard\Step::make('Aparência')
            ->icon('heroicon-o-sparkles')
            ->description('Visual e publicação')
            ->schema([
                Forms\Components\Tabs::make('appearance')
                    ->contained(false)
                    ->tabs([
                        Forms\Components\Tabs\Tab::make('Estilo')
                            ->icon('heroicon-m-swatch')
                            ->schema([
                                Forms\Components\ToggleButtons::make('content.background_color')
                                    ->label('Cor de fundo')
                                    ->helperText('Cor preenchida atrás de todo o conteúdo.')
                                    ->inline()
                                    ->options([
                                        'bg-transparent' => 'Transparente',
                                        'bg-white' => 'Claro',
                                        'bg-muted/30' => 'Cinza',
                                        'bg-foreground text-background' => 'Escuro',
                                        'bg-primary text-primary-foreground' => 'Cor Principal',
                                    ])
                                    ->icons([
                                        'bg-transparent' => 'heroicon-o-stop',
                                        'bg-white' => 'heroicon-o-sun',
                                        'bg-muted/30' => 'heroicon-o-cloud',
                                        'bg-foreground text-background' => 'heroicon-o-moon',
                                        'bg-primary text-primary-foreground' => 'heroicon-o-star',
                                    ])
                                    ->default('bg-transparent'),
                                Forms\Components\ToggleButtons::make('content.text_color')
                                    ->label('Cor do texto')
                                    ->helperText('Use texto claro em fundos escuros para não perder a leitura.')
                                    ->inline()
                                    ->grouped()
                                    ->options([
                                        'light' => 'Texto Escuro',
                                        'dark' => 'Texto Claro',
                                    ])
                                    ->icons([
                                        'light' => 'heroicon-o-pencil',
                                        'dark' => 'heroicon-o-pencil-square',
                                    ])
                                    ->default('light'),
                                Forms\Components\Fieldset::make('Fundo com imagem')
                                    ->columns(1)
                                    ->schema([
                                        Forms\Components\FileUpload::make('content.background_image')
                                            ->label('Imagem de fundo')
                                            ->helperText('Imagem atrás do conteúdo da seção.')
                                            ->image()
                                            ->imageEditor()
                                            ->imagePreviewHeight('120')
                                            ->directory('sections/backgrounds')
                                            ->live(),
                                        Forms\Components\ToggleButtons::make('content.background_image_fit')
                                            ->label('Preenchimento')
                                            ->helperText('Como a imagem se ajusta ao fundo.')
                                            ->options([
                                                'cover' => 'Preencher (Cover)',
                                                'contain' => 'Conter (Contain)',
                                                '100% auto' => 'Ajustar Largura (100%)',
                                                'auto' => 'Original (Auto)',
                                                'custom' => 'Personalizado (%)',
                                            ])
                                            ->icons([
                                                'cover' => 'heroicon-o-arrows-pointing-out',
                                                'contain' => 'heroicon-o-arrows-pointing-in',
                                                '100% auto' => 'heroicon-o-arrows-right-left',
                                                'auto' => 'heroicon-o-photo',
                                                'custom' => 'heroicon-o-adjustments-horizontal',
                                            ])
                                            ->default('cover')
                                            ->inline()
                                            ->live()
                                            ->visible($hasImage),
                                        Forms\Components\TextInput::make('content.background_image_scale')
                                            ->label('Tamanho da imagem (%)')
                                            ->prefixIcon('heroicon-m-arrows-pointing-out')
                                            ->numeric()
                                            ->default(50)
                                            ->minValue(1)
                                            ->maxValue(200)
                                            ->step(1)
                                            ->suffix('%')
                                            ->helperText('Tamanho percentual mantendo a proporção (ex: 50).')
                                            ->visible(fn (Forms\Get $get) => $hasImage($get) && $get('content.background_image_fit') === 'custom'),
                                        Forms\Components\ToggleButtons::make('content.background_image_position')
                                            ->label('Alinhamento')
                                            ->helperText('Para onde a imagem é alinhada.')
                                            ->options([
                                                'center' => 'Centro',
                                                'left center' => 'Esquerda',
                                                'right center' => 'Direita',
                                                'center top' => 'Topo Centro',
                                                'center bottom' => 'Base Centro',
                                            ])
                                            ->icons([
                                                'center' => 'heroicon-o-arrows-pointing-in',
                                                'left center' => 'heroicon-o-arrow-left',
                                                'right center' => 'heroicon-o-arrow-right',
                                                'center top' => 'heroicon-o-arrow-up',
                                                'center bottom' => 'heroicon-o-arrow-down',
                                            ])
                                            ->default('center')
                                            ->inline()
                                            ->visible($hasImage),
                                        Forms\Components\Select::make('content.background_image_opacity')
                                            ->label('Opacidade da imagem')
                                            ->prefixIcon('heroicon-m-eye')
                                            ->options($percentages([10, 20, 30, 40, 50, 60, 70, 80, 90, 100]))
                                            ->default('100')
                                            ->helperText('Mistura a imagem com a cor de fundo.')
                                            ->visible($hasImage),
                                        Forms\Components\TextInput::make('content.background_image_pull_up')
                                            ->label('Ajuste vertical da imagem')
                                            ->prefixIcon('heroicon-m-arrows-up-down')
                                            ->numeric()
                                            ->minValue(-100)
                                            ->maxValue(100)
                                            ->step(5)
                                            ->suffix('%')
                                            ->default(0)
                                            ->helperText('Positivo sobe, negativo desce. 0 = posição original.')
                                            ->visible($hasImage),
                                        Forms\Components\Toggle::make('content.background_overlay_enabled')
                                            ->label('Habilitar overlay')
                                            ->onIcon('heroicon-m-check')->offIcon('heroicon-m-x-mark')
                                            ->default(false)
                                            ->live()
                                            ->helperText('Camada de cor sobre a imagem para melhorar a leitura do texto.'),
                                        Forms\Components\ToggleButtons::make('content.background_overlay_type')
                                            ->label('Cor do overlay')
                                            ->helperText('Estilo da película.')
                                            ->options([
                                                'light' => 'Claro',
                                                'dark' => 'Escuro',
                                            ])
                                            ->icons([
                                                'light' => 'heroicon-o-sun',
                                                'dark' => 'heroicon-o-moon',
                                            ])
                                            ->default('dark')
                                            ->inline()
                                            ->grouped()
                                            ->visible(fn (Forms\Get $get) => $get('content.background_overlay_enabled') === true),
                                        Forms\Components\Select::make('content.background_overlay_opacity')
                                            ->label('Opacidade do overlay')
                                            ->prefixIcon('heroicon-m-adjustments-horizontal')
                                            ->helperText('Nível de transparência do overlay.')
                                            ->options($percentages([10, 20, 30, 40, 50, 60, 70, 80, 90]))
                                            ->default('50')
                                            ->visible(fn (Forms\Get $get) => $get('content.background_overlay_enabled') === true),
                                    ]),
                            ]),
                        Forms\Components\Tabs\Tab::make('Publicação')
                            ->icon('heroicon-m-globe-alt')
                            ->schema([
                                Forms\Components\Toggle::make('is_active')
                                    ->label('Ativo')
                                    ->onIcon('heroicon-m-check')->offIcon('heroicon-m-x-mark')
                                    ->default(true)
                                    ->helperText('Se inativo, a seção não aparece no site e usa o conteúdo estático.'),
                                Forms\Components\TextInput::make('sort_order')
                                    ->label('Ordem')
                                    ->prefixIcon('heroicon-m-bars-arrow-down')
                                    ->numeric()
                                    ->helperText('Posição na listagem de seções.')
                                    ->minValue(0),
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
                    ->boolean(),
                Tables\Columns\TextColumn::make('slug')
                    ->label('Slug')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Nome')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('type')
                    ->label('Tipo')
                    ->formatStateUsing(fn (string $state): string => ContentSection::typeLabels()[$state] ?? $state)
                    ->sortable(),
                Tables\Columns\TextColumn::make('sort_order')
                    ->label('Ordem')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Atualizado')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->filters([
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
            'index' => Pages\ListContentSections::route('/'),
            'create' => Pages\CreateContentSection::route('/create'),
            'edit' => Pages\EditContentSection::route('/{record}/edit'),
        ];
    }
}
