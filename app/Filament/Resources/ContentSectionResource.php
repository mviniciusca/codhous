<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ContentSectionResource\Pages;
use App\Models\ContentSection;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ContentSectionResource extends Resource
{
    protected static ?string $model = ContentSection::class;

    protected static ?string $navigationGroup = 'Website';
    protected static ?int $navigationSort = 2;

    protected static ?string $navigationIcon = 'heroicon-o-squares-2x2';

    protected static ?string $navigationLabel = 'Seções do site';

    protected static ?string $modelLabel = 'Seção';

    protected static ?string $pluralModelLabel = 'Seções';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Wizard::make([
                    Forms\Components\Wizard\Step::make('1. Tipo da Seção')
                        ->icon('heroicon-o-squares-2x2')
                        ->description('Escolha o formato e a função da seção.')
                        ->schema([
                            Forms\Components\Section::make('Identificação')
                                ->description('Informações básicas de identificação e configuração da seção no sistema.')
                                ->icon('heroicon-o-identification')
                                ->schema([
                                        Forms\Components\ToggleButtons::make('type')
                                            ->icons([
                                                ContentSection::TYPE_HERO => 'heroicon-o-star',
                                                ContentSection::TYPE_PARTNERS => 'heroicon-o-building-office',
                                                ContentSection::TYPE_COMMERCIAL_PARTNERS => 'heroicon-o-briefcase',
                                                ContentSection::TYPE_SERVICES => 'heroicon-o-wrench-screwdriver',
                                                ContentSection::TYPE_FAQ => 'heroicon-o-question-mark-circle',
                                                ContentSection::TYPE_TESTIMONIALS => 'heroicon-o-chat-bubble-left-right',
                                                ContentSection::TYPE_COVERAGE => 'heroicon-o-map',
                                                ContentSection::TYPE_DIFFERENTIALS => 'heroicon-o-sparkles',
                                                ContentSection::TYPE_TIMELINE => 'heroicon-o-clock',
                                                ContentSection::TYPE_CTA_CONTACT => 'heroicon-o-phone',
                                                ContentSection::TYPE_CONTACT_BANNER => 'heroicon-o-megaphone',
                                                ContentSection::TYPE_BUDGET_FORM => 'heroicon-o-currency-dollar',
                                                ContentSection::TYPE_CALCULATOR => 'heroicon-o-calculator',
                                                ContentSection::TYPE_PAYMENT_OFFER => 'heroicon-o-credit-card',
                                                ContentSection::TYPE_SIMPLE_BANNER => 'heroicon-o-photo',
                                                ContentSection::TYPE_TEAM => 'heroicon-o-users',
                                                ContentSection::TYPE_SHOWCASE => 'heroicon-o-camera',
                                                ContentSection::TYPE_EQUIPMENT_SHOWCASE => 'heroicon-o-truck',
                                            ])
                                            ->inline()
                                            ->columnSpanFull()
                                            ->label('Tipo da seção')
                                            ->options(ContentSection::typeLabels())
                                            ->required()
                                            ->disabled(fn (string $operation): bool => $operation === 'edit')
                                            ->helperText('Define os campos de conteúdo disponíveis. Não pode ser alterado depois de criado.')
                                            ->live()
                                            ->afterStateUpdated(function (Forms\Set $set, ?string $state) {
                                                if ($state) {
                                                    $set('slug', ContentSection::slugForType($state));
                                                    $set('name', ContentSection::typeLabels()[$state] ?? $state);
                                                }
                                            }),
                                        Forms\Components\TextInput::make('slug')
                                            ->label('Slug (identificador único)')
                                            ->required()
                                            ->disabled(fn (string $operation): bool => $operation === 'edit')
                                            ->unique(ignoreRecord: true)
                                            ->maxLength(255)
                                            ->helperText('Identificador no sistema. Não editável após criação.'),
                                        Forms\Components\TextInput::make('name')
                                            ->label('Nome (admin)')
                                            ->required()
                                            ->maxLength(255)
                                            ->helperText('Nome para identificação interna nesta lista.'),
                                        ])->columns(2),
                        ]),
                    Forms\Components\Wizard\Step::make('2. Conteúdo Principal')
                        ->icon('heroicon-o-document-text')
                        ->description('Preencha os textos, imagens e dados da seção.')
                        ->schema([


                Forms\Components\Section::make('Cabeçalho (opcional)')
                    ->icon('heroicon-o-bars-3-bottom-left')
                    ->description('Subtítulo, título e descrição exibidos no topo da seção.')
                    ->schema([
                        Forms\Components\Group::make([
                            Forms\Components\Toggle::make('content.header.visible')
                                ->label('Exibir Cabeçalho')
                                ->onIcon('heroicon-m-check')->offIcon('heroicon-m-x-mark')
                                ->helperText('Liga ou desliga a exibição do cabeçalho.')
                                ->default(true),
                            Forms\Components\ToggleButtons::make('content.header.alignment')
                                ->label('Alinhamento')
                                ->helperText('Alinhamento do texto no cabeçalho.')
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
                                ->default('center'),
                        ])->columns(2)->columnSpanFull(),
                        Forms\Components\TextInput::make('content.header.subtitle')
                            ->label('Pré-título')
                            ->helperText('Aparece com destaque acima do título.')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('content.header.title')
                            ->label('Título')
                            ->helperText('Título principal da seção.')
                            ->maxLength(255),
                        Forms\Components\Textarea::make('content.header.description')
                            ->label('Descrição')
                            ->helperText('Texto explicativo ou subtítulo abaixo do título principal.')
                            ->rows(2)
                            ->columnSpanFull(),
                    ])
                    ->columns(2)
                    ->collapsible(),

                // Hero
                Forms\Components\Section::make('Hero Section')
                    ->description('Destaque principal no topo da página.')
                    ->icon('heroicon-o-presentation-chart-line')
                    ->schema([
                        Forms\Components\Tabs::make('HeroTabs')->tabs([
                            Forms\Components\Tabs\Tab::make('Mídia e Layout')
                                ->icon('heroicon-o-photo')
                                ->schema([
                                    Forms\Components\Grid::make(2)->schema([
                                        Forms\Components\ToggleButtons::make('content.layout')
                                            ->label('Layout')
                                            ->helperText('Escolha o estilo de exibição da seção principal.')
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
                                        Forms\Components\ToggleButtons::make('content.image_alignment')
                                            ->label('Alinhamento da Imagem / Vídeo')
                                            ->helperText('Ajuste o foco visual da mídia de fundo.')
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
                                            ->inline(),
                                        Forms\Components\FileUpload::make('content.image')
                                            ->label('Imagem de Fundo')
                                            ->helperText('Imagem principal que ficará no fundo da seção.')
                                            ->image()
                                            ->directory('hero'),
                                        Forms\Components\FileUpload::make('content.video')
                                            ->label('Vídeo de Fundo (.mp4)')
                                            ->helperText('Se enviado, será exibido no lugar da imagem de fundo. Recomendado até 15MB.')
                                            ->acceptedFileTypes(['video/mp4'])
                                            ->directory('hero'),
                                        Forms\Components\Group::make([
                                            Forms\Components\Toggle::make('content.overlay_enabled')
                                                ->label('Ativar Overlay')
                                                ->onIcon('heroicon-m-check')->offIcon('heroicon-m-x-mark')
                                                ->helperText('Adiciona uma camada sobre a imagem de fundo para melhorar a leitura do texto.')
                                                ->default(true)
                                                ->live(),
                                            Forms\Components\ToggleButtons::make('content.overlay_theme')
                                                ->label('Tema do Overlay')
                                                ->helperText('Define se a película protetora será escura ou clara.')
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
                                                ->visible(fn (\Filament\Forms\Get $get) => $get('content.overlay_enabled')),
                                        ])->columns(2)->columnSpanFull(),
                                    ]),

                                ]),
                                
                            Forms\Components\Tabs\Tab::make('Slideshow')
                                ->icon('heroicon-o-rectangle-stack')
                                ->schema([
                                    Forms\Components\Toggle::make('content.show_slideshow')->onIcon('heroicon-m-check')->offIcon('heroicon-m-x-mark')
                                        ->label('Habilitar Slideshow (Carrossel no fundo)')
                                        ->helperText('Se ativo, exibirá um carrossel rotativo atrás da Hero em vez da imagem/vídeo fixo.')
                                        ->default(false)
                                        ->live(),
                                    Forms\Components\Repeater::make('content.slideshow')
                                        ->label('Slides de Fundo')
                                        ->schema([
                                            Forms\Components\FileUpload::make('image')
                                                ->label('Imagem')
                                                ->helperText('Envie a imagem para o slide.')
                                                ->image()
                                                ->directory('hero')
                                                ->required(),
                                            Forms\Components\ToggleButtons::make('image_alignment')
                                                ->label('Alinhamento da Imagem')
                                                ->helperText('Qual parte da imagem deve ficar em foco.')
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
                                        ->columns(2)
                                        ->collapsible()
                                        ->cloneable()
                                        ->maxItems(3)
                                        ->itemLabel(function (array $state): string {
                                            $image = $state['image'] ?? null;
                                            if (is_array($image)) {
                                                $image = array_values($image)[0] ?? null;
                                            }
                                            return $image ? 'Imagem: ' . basename((string) $image) : 'Novo Slide';
                                        })
                                        ->addActionLabel('Adicionar Imagem')
                                        ->visible(fn ($get) => $get('content.show_slideshow')),
                                ]),
                                
                            Forms\Components\Tabs\Tab::make('Botões de Ação')
                                ->icon('heroicon-o-cursor-arrow-rays')
                                ->schema([
                                    Forms\Components\Toggle::make('content.show_action_buttons')->onIcon('heroicon-m-check')->offIcon('heroicon-m-x-mark')
                                        ->label('Exibir Botões de Ação')
                                        ->helperText('Habilite para mostrar os botões adicionais no banner.')
                                        ->default(true)
                                        ->live(),
                                    Forms\Components\Grid::make(2)
                                        ->schema([
                                            Forms\Components\Group::make([
                                                Forms\Components\TextInput::make('content.primary_button_text')->label('Texto Botão 1')->helperText('Cor principal (fundo sólido)'),
                                                Forms\Components\TextInput::make('content.primary_button_url')->label('Link Botão 1')->helperText('Para onde o botão leva (ex: /#contato)'),
                                                Forms\Components\ToggleButtons::make('content.primary_button_icon_select')
                                                    ->label('Ícone rápido')
                                                    ->helperText('Escolha um ícone predefinido ou defina um personalizado.')
                                                    ->options([
                                                        'arrow-right' => 'Seta',
                                                        'phone' => 'Telefone',
                                                        'message-circle' => 'WhatsApp',
                                                        'calculator' => 'Calculadora',
                                                        'outro' => 'Outro',
                                                    ])
                                                    ->icons([
                                                        'arrow-right' => 'heroicon-o-arrow-right',
                                                        'phone' => 'heroicon-o-phone',
                                                        'message-circle' => 'heroicon-o-chat-bubble-oval-left-ellipsis',
                                                        'calculator' => 'heroicon-o-calculator',
                                                        'outro' => 'heroicon-o-magnifying-glass',
                                                    ])
                                                    ->inline()
                                                    ->live(),
                                                Forms\Components\TextInput::make('content.primary_button_icon')
                                                    ->label('Nome do Ícone (Botão 1)')
                                                    ->placeholder('ex: arrow-right')
                                                    ->helperText('Busque o nome do ícone em lucide.dev/icons')
                                                    ->visible(fn (\Filament\Forms\Get $get) => $get('content.primary_button_icon_select') === 'outro'),
                                            ])->columns(1),
                                            Forms\Components\Group::make([
                                                Forms\Components\TextInput::make('content.secondary_button_text')->label('Texto Botão 2')->helperText('Cor secundária (fundo transparente com borda)'),
                                                Forms\Components\TextInput::make('content.secondary_button_url')->label('Link Botão 2')->helperText('Para onde o botão leva (ex: /#servicos)'),
                                                Forms\Components\ToggleButtons::make('content.secondary_button_icon_select')
                                                    ->label('Ícone rápido')
                                                    ->helperText('Escolha um ícone predefinido ou defina um personalizado.')
                                                    ->options([
                                                        'arrow-right' => 'Seta',
                                                        'phone' => 'Telefone',
                                                        'message-circle' => 'WhatsApp',
                                                        'calculator' => 'Calculadora',
                                                        'outro' => 'Outro',
                                                    ])
                                                    ->icons([
                                                        'arrow-right' => 'heroicon-o-arrow-right',
                                                        'phone' => 'heroicon-o-phone',
                                                        'message-circle' => 'heroicon-o-chat-bubble-oval-left-ellipsis',
                                                        'calculator' => 'heroicon-o-calculator',
                                                        'outro' => 'heroicon-o-magnifying-glass',
                                                    ])
                                                    ->inline()
                                                    ->live(),
                                                Forms\Components\TextInput::make('content.secondary_button_icon')
                                                    ->label('Nome do Ícone (Botão 2)')
                                                    ->placeholder('ex: phone')
                                                    ->helperText('Busque o nome do ícone em lucide.dev/icons')
                                                    ->visible(fn (\Filament\Forms\Get $get) => $get('content.secondary_button_icon_select') === 'outro'),
                                            ])->columns(1),
                                        ])
                                        ->visible(fn (\Filament\Forms\Get $get) => $get('content.show_action_buttons') === true),
                                ]),
                                
                            Forms\Components\Tabs\Tab::make('Estatísticas')
                                ->icon('heroicon-o-chart-bar')
                                ->schema([
                                    Forms\Components\Toggle::make('content.show_stats')->onIcon('heroicon-m-check')->offIcon('heroicon-m-x-mark')
                                        ->label('Exibir Estatísticas')
                                        ->helperText('Habilite para exibir o bloco de estatísticas no banner.')
                                        ->default(true),
                                    Forms\Components\Repeater::make('content.stats')
                                        ->label('Estatísticas (Máx. 3)')
                                        ->helperText('Números importantes em destaque (ex: +500 Projetos).')
                                        ->schema([
                                            Forms\Components\TextInput::make('value')->label('Valor')->helperText('Ex: +500')->required(),
                                            Forms\Components\TextInput::make('label')->label('Rótulo')->helperText('Ex: Projetos Entregues')->required(),
                                        ])
                                        ->columns(2)
                                        ->collapsible()
                                        ->cloneable()
                                        ->maxItems(3)
                                        ->itemLabel(fn (array $state): ?string => trim(($state['value'] ?? '') . ' ' . ($state['label'] ?? '')) ?: null)
                                ]),
                        ]),
                    ])
                    ->visible(fn ($get): bool => $get('type') === ContentSection::TYPE_HERO)
                    ->collapsible(),

                // FAQ
                Forms\Components\Section::make('Perguntas e respostas')
                    ->description('Gerencie as perguntas frequentes exibidas nesta seção.')
                    ->icon('heroicon-o-question-mark-circle')
                    ->schema([
                        Forms\Components\Repeater::make('content.items')
                            ->label('Itens FAQ')
                            ->schema([
                                Forms\Components\TextInput::make('question')->label('Pergunta')->helperText('A dúvida do usuário.')->required(),
                                Forms\Components\Textarea::make('answer')->label('Resposta')->helperText('A resposta para a dúvida.')->required()->rows(2),
                            ])
                            ->columns(1)
                            ->itemLabel(fn (array $state): ?string => $state['question'] ?? null),
                    ])
                    ->visible(fn ($get): bool => $get('type') === ContentSection::TYPE_FAQ)
                    ->collapsible(),

                // Design e Layout (Partners)
                Forms\Components\Section::make('Design e Layout')
                    ->description('Configurações visuais da seção de parceiros.')
                    ->icon('heroicon-o-paint-brush')
                    ->schema([
                        Forms\Components\ToggleButtons::make('content.layout')
                            ->label('Layout')
                            ->options([
                                'slider' => 'Slider (Carrossel Contínuo)',
                                'grid' => 'Grid (Lado a Lado)',
                            ])
                            ->default('slider')
                            ->inline(),
                    ])
                    ->visible(fn ($get): bool => $get('type') === ContentSection::TYPE_PARTNERS)
                    ->collapsible(),

                // Partners
                Forms\Components\Section::make('Parceiros')
                    ->description('Adicione as logomarcas ou nomes das empresas parceiras.')
                    ->icon('heroicon-o-building-office-2')
                    ->schema([
                        Forms\Components\Repeater::make('content.items')
                            ->label('Empresas / Parceiros')
                            ->schema([
                                Forms\Components\FileUpload::make('logo')
                                    ->label('Logo da Empresa')
                                    ->image()
                                    ->directory('partners')
                                    ->columnSpanFull(),
                                Forms\Components\TextInput::make('name')->label('Nome')->helperText('Nome do parceiro.')->required(),
                                Forms\Components\TextInput::make('icon')
                                    ->label('Ícone (Lucide)')
                                    ->helperText('Nome do ícone correspondente.')
                                    ->placeholder('building-2, hard-hat, factory...')
                                    ->maxLength(50),
                            ])
                            ->columns(2)
                            ->grid(2)
                            ->itemLabel(fn (array $state): ?string => $state['name'] ?? null),
                    ])
                    ->visible(fn ($get): bool => $get('type') === ContentSection::TYPE_PARTNERS)
                    ->collapsible(),

                // Commercial Partners
                Forms\Components\Section::make('Parceiros Comerciais (Automático)')
                    ->description('Exibe os parceiros cadastrados em "Empresa > Parceiros". Você pode personalizar a imagem principal e os tópicos em destaque.')
                    ->icon('heroicon-o-building-office')
                    ->schema([
                        Forms\Components\FileUpload::make('content.main_image')
                            ->label('Imagem Principal (Direita)')
                            ->helperText('A imagem que ficará ao lado do texto (ex: aperto de mãos).')
                            ->image()
                            ->directory('sections'),
                        Forms\Components\Repeater::make('content.features')
                            ->label('Tópicos de Destaque')
                            ->schema([
                                Forms\Components\TextInput::make('title')->label('Título')->required(),
                                Forms\Components\TextInput::make('icon')
                                    ->label('Ícone (Lucide)')
                                    ->default('check-circle')
                                    ->required(),
                            ])
                            ->columns(2)
                            ->grid(2)
                            ->maxItems(4)
                            ->itemLabel(fn (array $state): ?string => $state['title'] ?? null),
                    ])
                    ->visible(fn ($get): bool => $get('type') === ContentSection::TYPE_COMMERCIAL_PARTNERS)
                    ->collapsible(),

                // Services
                Forms\Components\Section::make('Serviços')
                    ->description('Cadastre os serviços oferecidos com seus respectivos detalhes.')
                    ->icon('heroicon-o-wrench-screwdriver')
                    ->schema([
                        Forms\Components\Repeater::make('content.items')
                            ->label('Serviços')
                            ->schema([
                                Forms\Components\TextInput::make('title')->label('Título')->helperText('Nome do serviço.')->required(),
                                Forms\Components\TextInput::make('subtitle')->label('Subtítulo (ex: por m³)')->helperText('Informação extra rápida.'),
                                Forms\Components\Textarea::make('description')->label('Descrição')->helperText('Detalhes do serviço.')->rows(2)->columnSpanFull(),
                                Forms\Components\ToggleButtons::make('_icon_preset')
                                    ->label('Ícone Lucide')
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
                                    ->label('Nome do Ícone Lucide')
                                    ->helperText('Digite o nome do ícone. Ex: camera, car')
                                    ->hidden(fn (Forms\Get $get) => $get('_icon_preset') !== 'outro')
                                    ->required(fn (Forms\Get $get) => $get('_icon_preset') === 'outro')
                                    ->dehydrated(true),
                                Forms\Components\TagsInput::make('bullets')->label('Lista de itens')->helperText('Tags ou tópicos do serviço.')->placeholder('Item'),
                                Forms\Components\TextInput::make('cta_label')->label('Texto do link')->helperText('O que vai escrito no botão.')->default('Solicitar Orçamento'),
                                Forms\Components\TextInput::make('cta_url')->label('URL do link')->helperText('Para onde o botão leva.')->default('#orcamento'),
                            ])
                            ->columns(2)
                            ->cloneable()
                            ->collapsible()
                            ->collapsed()
                            ->itemLabel(fn (array $state): ?string => $state['title'] ?? null),
                    ])
                    ->visible(fn ($get): bool => $get('type') === ContentSection::TYPE_SERVICES)
                    ->collapsible(),

                // Testimonials
                Forms\Components\Section::make('Depoimentos')
                    ->description('Adicione os depoimentos e avaliações de clientes.')
                    ->icon('heroicon-o-chat-bubble-left-right')
                    ->schema([
                        Forms\Components\Repeater::make('content.items')
                            ->label('Depoimentos')
                            ->schema([
                                Forms\Components\Textarea::make('quote')->label('Citação')->helperText('O texto do depoimento.')->required()->rows(3)->columnSpanFull(),
                                Forms\Components\TextInput::make('author_name')->label('Nome do autor')->helperText('Pessoa que deu o depoimento.')->required(),
                                Forms\Components\TextInput::make('author_role')->label('Cargo / Obra')->helperText('Ex: Cliente Codhous'),
                                Forms\Components\TextInput::make('stars')->label('Estrelas (1-5)')->helperText('Nota de 1 a 5.')->numeric()->minValue(1)->maxValue(5)->default(5),
                                Forms\Components\FileUpload::make('avatar')->label('Foto do Perfil (Opcional)')->image()->directory('testimonials/avatars')->avatar(),
                                Forms\Components\FileUpload::make('image')->label('Imagem da Obra (Opcional)')->image()->directory('testimonials/images')->columnSpanFull(),
                            ])
                            ->columns(2)
                            ->cloneable()
                            ->collapsible()
                            ->collapsed()
                            ->itemLabel(fn (array $state): ?string => $state['author_name'] ?? null),
                    ])
                    ->visible(fn ($get): bool => $get('type') === ContentSection::TYPE_TESTIMONIALS)
                    ->collapsible(),

                // Team
                Forms\Components\Section::make('Nosso Time')
                    ->description('Apresente os membros da equipe e profissionais da empresa.')
                    ->icon('heroicon-o-users')
                    ->schema([
                        Forms\Components\Repeater::make('content.items')
                            ->label('Membros da Equipe')
                            ->schema([
                                Forms\Components\FileUpload::make('avatar')
                                    ->label('Foto')
                                    ->image()
                                    ->directory('team/avatars')
                                    ->avatar()
                                    ->helperText('Formato ideal: retangular ou quadrado. Ficará no topo do card.')
                                    ->columnSpanFull(),
                                Forms\Components\TextInput::make('name')
                                    ->label('Nome')
                                    ->helperText('Nome completo do membro.')
                                    ->required()
                                    ->columnSpan(1),
                                Forms\Components\TextInput::make('role')
                                    ->label('Cargo (Laranja)')
                                    ->helperText('Cargo principal em destaque.')
                                    ->required()
                                    ->columnSpan(1),
                                Forms\Components\Group::make([
                                    Forms\Components\ToggleButtons::make('icon_type')
                                        ->label('Ícone do Cargo')
                                        ->helperText('Ícone que aparece ao lado do cargo (padrão Lucide Icons).')
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
                                        ->label('Nome do Ícone Customizado')
                                        ->helperText('Acesse lucide.dev/icons para ver os nomes (ex: "star", "zap").')
                                        ->visible(fn (\Filament\Forms\Get $get) => $get('icon_type') === 'other')
                                        ->required(fn (\Filament\Forms\Get $get) => $get('icon_type') === 'other'),
                                ])->columns(2)->columnSpanFull(),
                                Forms\Components\TextInput::make('sub_role')
                                    ->label('Sub-cargo (Cinza, Opcional)')
                                    ->helperText('Informação extra abaixo do cargo principal.')
                                    ->columnSpanFull(),
                                Forms\Components\Fieldset::make('Destaque 1')
                                    ->schema([
                                        Forms\Components\TextInput::make('highlight_1_icon')->label('Ícone (Lucide)')->default('building-2')->helperText('Ex: building-2'),
                                        Forms\Components\TextInput::make('highlight_1_title')->label('Título (Negrito)')->helperText('Ex: 12+ anos'),
                                        Forms\Components\TextInput::make('highlight_1_subtitle')->label('Subtítulo')->helperText('Ex: de experiência'),
                                    ])->columns(1)->columnSpan(1),
                                Forms\Components\Fieldset::make('Destaque 2')
                                    ->schema([
                                        Forms\Components\TextInput::make('highlight_2_icon')->label('Ícone (Lucide)')->default('users')->helperText('Ex: users'),
                                        Forms\Components\TextInput::make('highlight_2_title')->label('Título (Negrito)')->helperText('Ex: Liderança'),
                                        Forms\Components\TextInput::make('highlight_2_subtitle')->label('Subtítulo')->helperText('Ex: e estratégia'),
                                    ])->columns(1)->columnSpan(1),
                            ])
                            ->columns(2)
                            ->cloneable()
                            ->collapsible()
                            ->collapsed()
                            ->itemLabel(fn (array $state): ?string => $state['name'] ?? null),
                    ])
                    ->visible(fn ($get): bool => $get('type') === ContentSection::TYPE_TEAM)
                    ->collapsible(),

                // Coverage
                Forms\Components\Section::make('Onde atuamos')
                    ->description('Selecione as cidades e configure os cards informativos.')
                    ->icon('heroicon-o-map-pin')
                    ->schema([
                        Forms\Components\Grid::make(2)->schema([
                            Forms\Components\Select::make('content.cities')
                                ->label('Cidades Atendidas')
                                ->multiple()
                                ->options(\App\Models\OperationArea::query()->where('is_active', true)->pluck('city', 'city'))
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
                                ->helperText('Selecione as cidades que deseja destacar. Os dados vêm do módulo de Áreas de Operação.'),
                            Forms\Components\FileUpload::make('content.background_media')
                                ->label('Imagem ou Vídeo de Fundo')
                                ->helperText('Envie uma imagem ou um vídeo curto (MP4) para sobrepor a seção.')
                                ->directory('coverage')
                                ->acceptedFileTypes(['image/*', 'video/mp4', 'video/webm', 'video/quicktime'])
                                ->maxSize(20480),
                        ]),
                        Forms\Components\Repeater::make('content.sidebar')
                            ->label('Cards laterais')
                            ->schema([
                                Forms\Components\Grid::make(2)->schema([
                                    Forms\Components\TextInput::make('title')->label('Título')->helperText('Título do card.')->required(),
                                    Forms\Components\TextInput::make('icon')->label('Ícone Lucide')->helperText('Opcional. Ex: map-pin, truck'),
                                    Forms\Components\Textarea::make('description')->label('Descrição')->helperText('Descrição do card.')->rows(2)->columnSpanFull(),
                                ])
                            ])
                            ->cloneable()
                            ->collapsible()
                            ->collapsed()
                            ->itemLabel(fn (array $state): ?string => $state['title'] ?? null)
                            ->columnSpanFull(),
                    ])
                    ->visible(fn ($get): bool => $get('type') === ContentSection::TYPE_COVERAGE)
                    ->collapsible(),

                // Differentials
                Forms\Components\Section::make('Diferenciais')
                    ->description('Destaque os principais diferenciais ou pilares da empresa.')
                    ->icon('heroicon-o-star')
                    ->schema([
                        Forms\Components\Repeater::make('content.items')
                            ->label('Itens')
                            ->schema([
                                Forms\Components\TextInput::make('icon')->label('Ícone Lucide')->helperText('Ícone do diferencial.')->placeholder('clock, microscope'),
                                Forms\Components\TextInput::make('title')->label('Título')->helperText('Nome do diferencial.')->required(),
                                Forms\Components\Textarea::make('description')->label('Descrição')->helperText('O que isso significa.')->rows(2),
                            ])
                            ->columns(1)
                            ->itemLabel(fn (array $state): ?string => $state['title'] ?? null),
                    ])
                    ->visible(fn ($get): bool => $get('type') === ContentSection::TYPE_DIFFERENTIALS)
                    ->collapsible(),

                // Timeline
                Forms\Components\Section::make('Etapas (Como funciona)')
                    ->description('Crie uma linha do tempo com o passo a passo do seu processo.')
                    ->icon('heroicon-o-clock')
                    ->schema([
                        Forms\Components\Repeater::make('content.steps')
                            ->label('Etapas')
                            ->schema([
                                Forms\Components\TextInput::make('step_label')->label('Rótulo (ex: Etapa 1)')->helperText('Indicador da etapa.')->required(),
                                Forms\Components\TextInput::make('title')->label('Título')->helperText('Nome da etapa.')->required(),
                                Forms\Components\Textarea::make('description')->label('Descrição')->helperText('Explicação do que ocorre.')->rows(2),
                                Forms\Components\TextInput::make('icon')->label('Ícone Lucide')->helperText('Ícone da etapa.')->placeholder('message-square-text, truck'),
                            ])
                            ->columns(1)
                            ->itemLabel(fn (array $state): ?string => $state['title'] ?? null),
                    ])
                    ->visible(fn ($get): bool => $get('type') === ContentSection::TYPE_TIMELINE)
                    ->collapsible(),

                // Contato
                Forms\Components\Section::make('Contato')
                    ->description('Configure os textos e botões do formulário de contato.')
                    ->icon('heroicon-o-envelope')
                    ->schema([
                        Forms\Components\TextInput::make('content.email_to')
                            ->label('E-mail de Destino (opcional)')
                            ->helperText('Se não preenchido, enviará para o e-mail padrão da empresa.'),
                        Forms\Components\Fieldset::make('Botão Orçamento')
                            ->schema([
                                Forms\Components\Toggle::make('content.budget_btn_enabled')->onIcon('heroicon-m-check')->offIcon('heroicon-m-x-mark')
                                    ->label('Mostrar botão')
                                    ->helperText('Liga ou desliga esse botão.')
                                    ->onIcon('heroicon-m-check')
                                    ->default(true),
                                Forms\Components\TextInput::make('content.budget_btn_title')
                                    ->label('Título')
                                    ->helperText('Texto maior.')
                                    ->default('Orçamento Grátis Online'),
                                Forms\Components\TextInput::make('content.budget_btn_subtitle')
                                    ->label('Subtítulo')
                                    ->helperText('Texto menor.')
                                    ->default('Faça uma cotação rápida agora'),
                                Forms\Components\TextInput::make('content.budget_btn_url')
                                    ->label('Link do Botão')
                                    ->helperText('URL ou ID da página para redirecionar. Ex: /#orcamento')
                                    ->default('/#orcamento')
                                    ->columnSpanFull(),
                            ])->columns(3),
                        Forms\Components\Fieldset::make('Botões Adicionais')
                            ->schema([
                                Forms\Components\Toggle::make('content.whatsapp_btn_enabled')->onIcon('heroicon-m-check')->offIcon('heroicon-m-x-mark')
                                    ->label('Mostrar WhatsApp')
                                    ->helperText('Puxa o número das configurações do site.')
                                    ->onIcon('heroicon-m-check')
                                    ->default(true),
                                Forms\Components\Toggle::make('content.email_btn_enabled')->onIcon('heroicon-m-check')->offIcon('heroicon-m-x-mark')
                                    ->label('Mostrar E-mail')
                                    ->helperText('Mostra o e-mail cadastrado acima.')
                                    ->onIcon('heroicon-m-check')
                                    ->default(true),
                                Forms\Components\Toggle::make('content.phone_btn_enabled')->onIcon('heroicon-m-check')->offIcon('heroicon-m-x-mark')
                                    ->label('Mostrar Telefone')
                                    ->helperText('Puxa do cadastro da empresa.')
                                    ->onIcon('heroicon-m-check')
                                    ->default(true),
                                Forms\Components\Toggle::make('content.address_enabled')->onIcon('heroicon-m-check')->offIcon('heroicon-m-x-mark')
                                    ->label('Mostrar Endereço')
                                    ->helperText('Puxa do cadastro da empresa.')
                                    ->onIcon('heroicon-m-check')
                                    ->default(true),
                            ])->columns(4),
                    ])
                    ->visible(fn ($get): bool => $get('type') === ContentSection::TYPE_CTA_CONTACT)
                    ->collapsible(),

                // Contact Banner (Atendimento)
                Forms\Components\Section::make('Banner de Atendimento')
                    ->description('Configure a chamada para atendimento rápido (WhatsApp, Ligação, E-mail).')
                    ->icon('heroicon-o-megaphone')
                    ->schema([
                        Forms\Components\TextInput::make('content.badge')
                            ->label('Badge (Texto Superior)')
                            ->helperText('Exibido pequeno acima do título.')
                            ->placeholder('ATENDIMENTO')
                            ->default('ATENDIMENTO'),
                        Forms\Components\TextInput::make('content.title')
                            ->label('Título')
                            ->helperText('Chamada principal.')
                            ->default('Fale conosco')
                            ->required(),
                        Forms\Components\Textarea::make('content.description')
                            ->label('Descrição')
                            ->helperText('Texto de apoio.')
                            ->default('Dúvidas, orçamento ou suporte: estamos prontos para atender você por telefone, WhatsApp ou e-mail.')
                            ->rows(2),
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\Toggle::make('content.whatsapp_enabled')->onIcon('heroicon-m-check')->offIcon('heroicon-m-x-mark')
                                    ->label('Botão WhatsApp')
                                    ->helperText('Ativa o botão do WhatsApp.')
                                    ->onIcon('heroicon-m-check')
                                    ->default(true),
                                Forms\Components\Toggle::make('content.call_enabled')->onIcon('heroicon-m-check')->offIcon('heroicon-m-x-mark')
                                    ->label('Botão Ligar')
                                    ->helperText('Ativa o botão de ligação.')
                                    ->onIcon('heroicon-m-check')
                                    ->default(true),
                                Forms\Components\Toggle::make('content.email_enabled')->onIcon('heroicon-m-check')->offIcon('heroicon-m-x-mark')
                                    ->label('Botão E-mail')
                                    ->helperText('Ativa o botão de e-mail.')
                                    ->onIcon('heroicon-m-check')
                                    ->default(true),
                            ]),
                    ])
                    ->visible(fn ($get): bool => $get('type') === ContentSection::TYPE_CONTACT_BANNER)
                    ->collapsible(),

                // Budget Form
                Forms\Components\Section::make('Formulário de Orçamento')
                    ->description('Exibe o wizard (passo a passo) do formulário de orçamento.')
                    ->icon('heroicon-o-calculator')
                    ->schema([
                        Forms\Components\Placeholder::make('info')
                            ->label('')
                            ->content('Esta seção não possui configurações específicas. Utilize o bloco "Cabeçalho (opcional)" acima para definir o título e a descrição. O formulário em si será exibido automaticamente com a aparência selecionada na aba "Configurações".'),
                    ])
                    ->visible(fn ($get): bool => $get('type') === ContentSection::TYPE_BUDGET_FORM)
                    ->collapsible(),

                // Calculator
                Forms\Components\Section::make('Calculadora de Volume')
                    ->description('Exibe a ferramenta de cálculo de volume de concreto.')
                    ->icon('heroicon-o-variable')
                    ->schema([
                        Forms\Components\Placeholder::make('info_calc')
                            ->label('')
                            ->content('Esta seção não possui configurações específicas. Utilize o bloco "Cabeçalho (opcional)" acima para definir o título e a descrição. A calculadora será exibida automaticamente com a aparência selecionada na aba "Configurações".'),
                    ])
                    ->visible(fn ($get): bool => $get('type') === ContentSection::TYPE_CALCULATOR)
                    ->collapsible(),

                // Showcase
                Forms\Components\Section::make('Showcase de Equipamentos')
                    ->description('Exibe a vitrine dinâmica de equipamentos com busca e filtros.')
                    ->icon('heroicon-o-view-columns')
                    ->schema([
                        Forms\Components\Placeholder::make('info_showcase')
                            ->label('')
                            ->content('Esta seção carregará automaticamente os equipamentos cadastrados no menu "Equipamentos". Utilize o bloco "Cabeçalho (opcional)" acima para definir o título (ex: "Equipamentos para cada etapa da sua obra") e a descrição.'),
                    ])
                    ->visible(fn ($get): bool => $get('type') === ContentSection::TYPE_SHOWCASE)
                    ->collapsible(),
                    
                // Payment Offer
                Forms\Components\Section::make('Oferta de Pagamento')
                    ->description('Exibe uma oferta de pagamento e meios de pagamento.')
                    ->icon('heroicon-o-credit-card')
                    ->schema([
                        Forms\Components\Grid::make(2)->schema([
                            Forms\Components\TextInput::make('content.button_label')->label(__('Texto do Botão'))->default('Fazer orçamento grátis'),
                            Forms\Components\TextInput::make('content.button_url')->label(__('URL do Botão (Deixe vazio p/ usar o WhatsApp)'))->helperText('Se vazio, enviará para o WhatsApp padrão.'),
                            Forms\Components\Toggle::make('content.show_payment_methods')->label(__('Exibir meios de pagamento?'))->default(true)->inline(false)->live()->columnSpanFull(),
                            Forms\Components\FileUpload::make('content.payment_methods_image')->image()->imageEditor()->directory('sections/payment')->label(__('Banner dos Meios de Pagamento (Cartões)'))->helperText('Recomendado imagem com fundo transparente (PNG/SVG) com as bandeiras dos cartões.')->columnSpanFull()->visible(fn (\Filament\Forms\Get $get) => $get('content.show_payment_methods') !== false),
                        ])
                    ])
                    ->visible(fn ($get): bool => $get('type') === ContentSection::TYPE_PAYMENT_OFFER)
                    ->collapsible(),
                    
                // Simple Banner
                Forms\Components\Section::make('Banner Simples (Imagem e Link)')
                    ->description('Exibe uma imagem clicável, ideal para chamadas promocionais rápidas.')
                    ->icon('heroicon-o-photo')
                    ->schema([
                        Forms\Components\FileUpload::make('content.image')
                            ->label('Imagem do Banner')
                            ->image()
                            ->imageEditor()
                            ->directory('sections/banners')
                            ->required()
                            ->columnSpanFull(),
                        Forms\Components\Grid::make(2)->schema([
                            Forms\Components\TextInput::make('content.link_url')
                                ->label('Link de Destino')
                                ->url()
                                ->placeholder('Ex: https://...'),
                            Forms\Components\Toggle::make('content.open_in_new_tab')
                                ->label('Abrir link em nova aba?')
                                ->default(true)
                                ->inline(false),
                        ])
                    ])
                    ->visible(fn ($get): bool => $get('type') === ContentSection::TYPE_SIMPLE_BANNER)
                    ->collapsible(),
                            ]),
                    Forms\Components\Wizard\Step::make('3. Aparência e Publicação')
                        ->icon('heroicon-o-sparkles')
                        ->description('Ajuste o visual geral e publique sua seção.')
                        ->schema([
                            Forms\Components\Section::make('Publicação')
                                ->icon('heroicon-o-globe-alt')
                                ->schema([
                                    Forms\Components\Toggle::make('is_active')
                                            ->label('Ativo')
                                            ->onIcon('heroicon-m-check')->offIcon('heroicon-m-x-mark')
                                            ->default(true)
                                            ->helperText('Se inativo, a seção não aparece no site e usa o conteúdo estático.'),
                                        Forms\Components\TextInput::make('sort_order')
                                            ->label('Ordem')
                                            ->numeric()
                                            ->helperText('Ordem na listagem.')
                                            ->minValue(0),
                                    
                                ])->columns(2),
                            Forms\Components\Section::make('Aparência Global')
                                ->description('Configurações visuais gerais aplicadas a esta seção.')
                                ->icon('heroicon-o-swatch')
                                ->schema([
                                        Forms\Components\ToggleButtons::make('content.background_color')
                                            ->label('Cor de Fundo')
                                            ->helperText('Define a cor de fundo preenchida atrás de todo o conteúdo.')
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
                                            ->label('Cor do Texto')
                                            ->helperText('Ajuste isso para que o texto não "suma" se o fundo for muito escuro.')
                                            ->inline()
                                            ->options([
                                                'light' => 'Texto Escuro',
                                                'dark' => 'Texto Claro',
                                            ])
                                            ->icons([
                                                'light' => 'heroicon-o-pencil',
                                                'dark' => 'heroicon-o-pencil-square',
                                            ])
                                            ->default('light'),
                                            
                                        Forms\Components\Fieldset::make('Fundo com Imagem')
                                            ->schema([
                                                Forms\Components\FileUpload::make('content.background_image')
                                                    ->label('Imagem de Fundo')
                                                    ->image()
                                                    ->imageEditor()
                                                    ->directory('sections/backgrounds')
                                                    ->helperText('Selecione uma imagem para o fundo da seção.')
                                                    ->live()
                                                    ->columnSpanFull(),
                                                Forms\Components\ToggleButtons::make('content.background_image_fit')
                                                    ->label('Preenchimento')
                                                    ->helperText('Como a imagem se ajusta no fundo.')
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
                                                    ->visible(fn (\Filament\Forms\Get $get) => filled($get('content.background_image'))),
                                                Forms\Components\TextInput::make('content.background_image_scale')
                                                    ->label('Tamanho da Imagem (%)')
                                                    ->numeric()
                                                    ->default(50)
                                                    ->minValue(1)
                                                    ->maxValue(200)
                                                    ->step(1)
                                                    ->suffix('%')
                                                    ->helperText('Defina o tamanho percentual mantendo a proporção (ex: 50).')
                                                    ->visible(fn (\Filament\Forms\Get $get) => filled($get('content.background_image')) && $get('content.background_image_fit') === 'custom'),
                                                Forms\Components\ToggleButtons::make('content.background_image_position')
                                                    ->label('Alinhamento')
                                                    ->helperText('Para onde a imagem será alinhada.')
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
                                                    ->visible(fn (\Filament\Forms\Get $get) => filled($get('content.background_image'))),
                                                Forms\Components\Select::make('content.background_image_opacity')
                                                    ->label('Opacidade da Imagem')
                                                    ->options([
                                                        '10' => '10%',
                                                        '20' => '20%',
                                                        '30' => '30%',
                                                        '40' => '40%',
                                                        '50' => '50%',
                                                        '60' => '60%',
                                                        '70' => '70%',
                                                        '80' => '80%',
                                                        '90' => '90%',
                                                        '100' => '100%',
                                                    ])
                                                    ->default('100')
                                                    ->helperText('Mistura a imagem com a Cor de Fundo.')
                                                    ->visible(fn (\Filament\Forms\Get $get) => filled($get('content.background_image'))),
                                                Forms\Components\TextInput::make('content.background_image_pull_up')
                                                    ->label('Ajuste de Imagem (Vertical)')
                                                    ->numeric()
                                                    ->minValue(-100)
                                                    ->maxValue(100)
                                                    ->step(5)
                                                    ->suffix('%')
                                                    ->default(0)
                                                    ->helperText('Desloca a imagem para cima (positivo) ou para baixo (negativo). 0 = posição original.')
                                                    ->visible(fn (\Filament\Forms\Get $get) => filled($get('content.background_image')))
                                                    ->columnSpanFull(),
                                                Forms\Components\Toggle::make('content.background_overlay_enabled')
                                                    ->label('Habilitar Overlay')
                                                    ->onIcon('heroicon-m-check')->offIcon('heroicon-m-x-mark')
                                                    ->default(false)
                                                    ->live()
                                                    ->columnSpanFull()
                                                    ->helperText('Adiciona uma camada de cor sobre a imagem de fundo para melhorar a legibilidade do texto.'),
                                                Forms\Components\ToggleButtons::make('content.background_overlay_type')
                                                    ->label('Cor do Overlay')
                                                    ->helperText('Define o estilo da película.')
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
                                                    ->visible(fn (\Filament\Forms\Get $get) => $get('content.background_overlay_enabled') === true),
                                                Forms\Components\Select::make('content.background_overlay_opacity')
                                                    ->label('Opacidade do Overlay')
                                                    ->helperText('Nível de transparência do overlay.')
                                                    ->options([
                                                        '10' => '10%',
                                                        '20' => '20%',
                                                        '30' => '30%',
                                                        '40' => '40%',
                                                        '50' => '50%',
                                                        '60' => '60%',
                                                        '70' => '70%',
                                                        '80' => '80%',
                                                        '90' => '90%',
                                                    ])
                                                    ->default('50')
                                                    ->visible(fn (\Filament\Forms\Get $get) => $get('content.background_overlay_enabled') === true),
                                            ])
                                            ->columns(3),
                                    ])->columns(2),
                        ]),
                ])->skippable()->columnSpanFull(),
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
