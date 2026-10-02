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
                            Forms\Components\Builder::make('content')
                                ->label('')
                                ->addActionLabel(__('Adicionar Novo Bloco'))
                                ->blocks([
                                    self::getPageHeaderBlock(),
                                    self::getCalculatorBlock(),
                                    self::getBudgetFormBlock(),
                                    self::getShowcaseBlock(),
                                    self::getMapBlock(),
                                    self::getRichTextBlock(),
                                    self::getPaymentOfferBlock(),
                                    self::getModuleReferenceBlock(),
                                ])
                                ->collapsible()
                                ->collapsed()
                                ->cloneable()
                                ->blockPickerColumns(2)
                                ->blockNumbers(false),
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

    protected static function getPartnersBlock(): Forms\Components\Builder\Block
    {
        return Forms\Components\Builder\Block::make('partners')
            ->label(__('Parceiros'))
            ->icon('heroicon-o-building-office')
            ->schema([
                Forms\Components\TextInput::make('title')->label(__('Título'))->helperText(__('Título da seção de parceiros.')),
                Forms\Components\Textarea::make('description')->label(__('Descrição'))->helperText(__('Breve texto sobre a parceria.')),
                Forms\Components\Repeater::make('items')
                    ->label(__('Parceiros'))
                    ->helperText(__('Cadastre as logomarcas ou nomes dos parceiros.'))
                    ->schema([
                        Forms\Components\TextInput::make('name')->label(__('Nome'))->helperText(__('Nome da empresa parceira.'))->required(),
                        Forms\Components\TextInput::make('icon')->label(__('Ícone (Lucide)'))->helperText(__('Nome do ícone Lucide, se houver.')),
                    ])->columns(2),
            ]);
    }

    protected static function getServicesBlock(): Forms\Components\Builder\Block
    {
        return Forms\Components\Builder\Block::make('services')
            ->label(__('Serviços'))
            ->icon('heroicon-o-wrench-screwdriver')
            ->schema([
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
            ]);
    }

    protected static function getTimelineBlock(): Forms\Components\Builder\Block
    {
        return Forms\Components\Builder\Block::make('timeline')
            ->label(__('Linha do Tempo (Etapas)'))
            ->icon('heroicon-o-clock')
            ->schema([
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
            ]);
    }

    protected static function getShowcaseBlock(): Forms\Components\Builder\Block
    {
        return Forms\Components\Builder\Block::make('showcase')
            ->label(__('Galeria de Obras (Showcase)'))
            ->icon('heroicon-o-camera')
            ->schema([
                Forms\Components\Grid::make(2)->schema([
                    Forms\Components\TextInput::make('badge')->label(__('Pré-título'))->helperText(__('Ex: NOSSAS OBRAS'))->placeholder('NOSSAS OBRAS'),
                    Forms\Components\TextInput::make('title')->label(__('Título'))->helperText(__('Título principal da galeria.'))->required(),
                    Forms\Components\Textarea::make('description')->label(__('Descrição'))->helperText(__('Breve texto explicativo da galeria.'))->columnSpanFull(),
                    Forms\Components\TextInput::make('limit')->numeric()->default(4)->label(__('Limite de itens'))->helperText(__('Quantidade máxima de obras a serem exibidas.')),
                ])
            ]);
    }

    protected static function getFaqBlock(): Forms\Components\Builder\Block
    {
        return Forms\Components\Builder\Block::make('faq')
            ->label(__('FAQ (Perguntas Frequentes)'))
            ->icon('heroicon-o-question-mark-circle')
            ->schema([
                Forms\Components\TextInput::make('title')->label(__('Título'))->helperText(__('Título da seção de perguntas frequentes.')),
                Forms\Components\Repeater::make('items')
                    ->label(__('Perguntas'))
                    ->helperText(__('Cadastre as perguntas e respostas.'))
                    ->schema([
                        Forms\Components\TextInput::make('question')->label(__('Pergunta'))->helperText(__('A dúvida frequente.'))->required(),
                        Forms\Components\Textarea::make('answer')->label(__('Resposta'))->helperText(__('A resposta para a dúvida.'))->required(),
                    ]),
            ]);
    }

    protected static function getTestimonialsBlock(): Forms\Components\Builder\Block
    {
        return Forms\Components\Builder\Block::make('testimonials')
            ->label(__('Depoimentos'))
            ->icon('heroicon-o-chat-bubble-bottom-center-text')
            ->schema([
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
            ]);
    }

    protected static function getCoverageBlock(): Forms\Components\Builder\Block
    {
        return Forms\Components\Builder\Block::make('coverage')
            ->label(__('Área de Atendimento'))
            ->icon('heroicon-o-map-pin')
            ->schema([
                Forms\Components\TextInput::make('title')->label(__('Título'))->helperText(__('Título da seção de área de cobertura.')),
                Forms\Components\Select::make('cities')
                    ->label(__('Cidades Atendidas'))
                    ->multiple()
                    ->options(\App\Models\OperationArea::query()->where('is_active', true)->pluck('city', 'city'))
                    ->helperText(__('Selecione as cidades que deseja destacar. Os dados vêm do módulo de Áreas de Operação.')),
            ]);
    }

    protected static function getCtaBlock(): Forms\Components\Builder\Block
    {
        return Forms\Components\Builder\Block::make('cta')
            ->label(__('Chamada para Ação (CTA)'))
            ->icon('heroicon-o-megaphone')
            ->schema([
                Forms\Components\TextInput::make('title')->label(__('Título'))->helperText(__('Título da chamada principal.')),
                Forms\Components\Textarea::make('subtitle')->label(__('Subtítulo'))->helperText(__('Texto de apoio da chamada.')),
                Forms\Components\TextInput::make('button_label')->label(__('Rótulo do Botão'))->helperText(__('Texto do botão de ação.')),
                Forms\Components\TextInput::make('button_url')->label(__('URL do Botão'))->helperText(__('Link para onde o botão deve levar.')),
            ]);
    }

    protected static function getDifferentialsBlock(): Forms\Components\Builder\Block
    {
        return Forms\Components\Builder\Block::make('differentials')
            ->label(__('Diferenciais (Pilar / Missão / Visão)'))
            ->icon('heroicon-o-shield-check')
            ->schema([
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
            ]);
    }

    protected static function getPageHeaderBlock(): Forms\Components\Builder\Block
    {
        return Forms\Components\Builder\Block::make('page_header')
            ->label(__('Cabeçalho da Página'))
            ->icon('heroicon-o-document-text')
            ->schema([
                Forms\Components\Grid::make(2)->schema([
                    Forms\Components\TextInput::make('badge')->label(__('Pré-título'))->helperText(__('Texto acima do título principal.'))->placeholder('NOSSOS SERVIÇOS'),
                    Forms\Components\TextInput::make('title')->label(__('Título Principal'))->helperText(__('Título grande da página.'))->required(),
                    Forms\Components\Textarea::make('description')->label(__('Descrição'))->helperText(__('Subtítulo ou texto descritivo do cabeçalho.'))->columnSpanFull(),
                    Forms\Components\Toggle::make('show_breadcrumbs')->label(__('Mostrar Breadcrumbs'))->helperText(__('Exibe o caminho de navegação (ex: Home > Serviços).'))->onIcon('heroicon-m-check')->default(true)->columnSpanFull(),
                    Forms\Components\FileUpload::make('background_image')->image()->directory('headers')->label(__('Imagem de Fundo (Opcional)'))->helperText(__('Imagem de fundo para o cabeçalho.'))->columnSpanFull(),
                ])
            ]);
    }



    protected static function getMapBlock(): Forms\Components\Builder\Block
    {
        return Forms\Components\Builder\Block::make('map')
            ->label(__('Mapa (Google Maps)'))
            ->icon('heroicon-o-map')
            ->schema([
                Forms\Components\TextInput::make('title')->label(__('Título'))->helperText(__('Título do mapa.')),
                Forms\Components\Textarea::make('iframe_code')
                    ->label(__('Código de Incorporação (iframe)'))
                    ->helperText(__('Cole aqui o <iframe> gerado pelo Google Maps.')),
            ]);
    }

    protected static function getRichTextBlock(): Forms\Components\Builder\Block
    {
        return Forms\Components\Builder\Block::make('rich_text')
            ->label(__('Texto Livre (Editor)'))
            ->icon('heroicon-o-document-text')
            ->schema([
                Forms\Components\RichEditor::make('content')->label(__('Conteúdo'))->helperText(__('Digite o conteúdo livremente usando o editor.'))->required(),
            ]);
    }

    protected static function getCalculatorBlock(): Forms\Components\Builder\Block
    {
        return Forms\Components\Builder\Block::make('calculator')
            ->label(__('Calculadora de Concreto'))
            ->icon('heroicon-o-calculator')
            ->schema([
                Forms\Components\TextInput::make('title')->label(__('Título'))->helperText(__('Título da calculadora.'))->default('Calculadora de Volume'),
            ]);
    }

    protected static function getBudgetFormBlock(): Forms\Components\Builder\Block
    {
        return Forms\Components\Builder\Block::make('budget_form')
            ->label(__('Formulário de Orçamento (Wizard)'))
            ->icon('heroicon-o-document-text')
            ->schema([
                Forms\Components\TextInput::make('title')->label(__('Título'))->helperText(__('Título do formulário de orçamento.'))->default('Solicitar Orçamento'),
                Forms\Components\Textarea::make('description')->label(__('Descrição'))->helperText(__('Instruções para o preenchimento.')),
            ]);
    }
    protected static function getContactBannerBlock(): Forms\Components\Builder\Block
    {
        return Forms\Components\Builder\Block::make('contact_banner')
            ->label(__('Banner de Atendimento (Call Actions)'))
            ->icon('heroicon-o-chat-bubble-left-right')
            ->schema([
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
            ]);
    }

    protected static function getPaymentOfferBlock(): Forms\Components\Builder\Block
    {
        return Forms\Components\Builder\Block::make('payment_offer')
            ->label(__('Oferta de Pagamento'))
            ->icon('heroicon-o-credit-card')
            ->schema([
                Forms\Components\Grid::make(2)->schema([
                    Forms\Components\TextInput::make('badge')->label(__('Pré-título'))->default('APROVEITE ESSA MEGA OPORTUNIDADE'),
                    Forms\Components\TextInput::make('title')->label(__('Título'))->default('Parcelamento em até 12x sem juros')->required(),
                    Forms\Components\Textarea::make('subtitle')->label(__('Subtítulo'))->default('ou com desconto no pagamento à vista em dinheiro ou com o pix.')->columnSpanFull(),
                    Forms\Components\TextInput::make('button_label')->label(__('Texto do Botão'))->default('Fazer orçamento grátis'),
                    Forms\Components\TextInput::make('button_url')->label(__('URL do Botão (Deixe vazio p/ usar o WhatsApp)'))->helperText('Se vazio, enviará para o WhatsApp padrão.'),
                    Forms\Components\FileUpload::make('background_image')->image()->directory('offers')->label(__('Imagem de Fundo (Opcional)'))->columnSpanFull(),
                    Forms\Components\FileUpload::make('payment_methods_image')->image()->directory('offers')->label(__('Banner dos Meios de Pagamento (Cartões)'))->helperText('Recomendado imagem com fundo transparente (PNG/SVG) com as bandeiras dos cartões.')->columnSpanFull(),
                ])
            ]);
    }

    protected static function getModuleReferenceBlock(): Forms\Components\Builder\Block
    {
        return Forms\Components\Builder\Block::make('module_reference')
            ->label(function (?array $state): string {
                if ($state === null) {
                    return __('Módulo Global (Seção Pronta)');
                }
                $sectionName = \App\Models\ContentSection::find($state['content_section_id'] ?? null)?->name;
                return $sectionName ? __('Seção pronta - ') . $sectionName : __('Módulo Global (Seção Pronta)');
            })
            ->icon('heroicon-o-squares-plus')
            ->schema([
                Forms\Components\ToggleButtons::make('content_section_id')
                    ->label(__('Seção de Conteúdo'))
                    ->options(\App\Models\ContentSection::query()->pluck('name', 'id'))
                    ->required()
                    ->inline()
                    ->live()
                    ->helperText(__('Selecione uma seção criada no módulo "Seções do site" para reutilizá-la aqui.')),
            ]);
    }
}
