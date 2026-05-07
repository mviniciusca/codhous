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
                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make(__('Conteúdo da Página'))
                            ->schema([
                                Forms\Components\Builder::make('content')
                                    ->label(__('Blocos de Conteúdo'))
                                        ->blocks([
                                            self::getPageHeaderBlock(),
                                            self::getHeroBlock(),
                                            self::getCalculatorBlock(),
                                            self::getBudgetFormBlock(),
                                            self::getPartnersBlock(),
                                            self::getServicesBlock(),
                                            self::getTimelineBlock(),
                                            self::getShowcaseBlock(),
                                            self::getFaqBlock(),
                                            self::getTestimonialsBlock(),
                                            self::getCoverageBlock(),
                                            self::getContactFormBlock(),
                                            self::getContactBannerBlock(),
                                            self::getMapBlock(),
                                            self::getDifferentialsBlock(),
                                            self::getCtaBlock(),
                                            self::getRichTextBlock(),
                                            self::getModuleReferenceBlock(),
                                        ])
                                    ->collapsible()
                                    ->collapsed(),
                            ]),
                    ])->columnSpan(['lg' => 2]),

                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make(__('Configurações'))
                            ->schema([
                                Forms\Components\TextInput::make('title')
                                    ->label(__('Título da Página'))
                                    ->required()
                                    ->lazy()
                                    ->afterStateUpdated(fn (Set $set, ?string $state) => $set('slug', Str::slug($state))),
                                Forms\Components\TextInput::make('slug')
                                    ->label(__('Slug'))
                                    ->required()
                                    ->unique(ignoreRecord: true),

                                Forms\Components\Toggle::make('is_visible')
                                    ->label(__('Página Visível'))
                                    ->default(true),
                                Forms\Components\TextInput::make('sort_order')
                                    ->label(__('Ordem'))
                                    ->numeric()
                                    ->default(0),
                            ]),
                        
                        Forms\Components\Section::make(__('SEO'))
                            ->schema([
                                Forms\Components\Textarea::make('meta.description')
                                    ->label(__('Meta Descrição')),
                            ])->collapsed(),
                    ])->columnSpan(['lg' => 1]),
            ])->columns(3);
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
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
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

    // Block Definitions

    protected static function getHeroBlock(): Forms\Components\Builder\Block
    {
        return Forms\Components\Builder\Block::make('hero')
            ->label(__('Hero (Destaque Principal)'))
            ->icon('heroicon-o-presentation-chart-line')
            ->schema([
                Forms\Components\Select::make('layout')
                    ->options([
                        'default' => 'Padrão (Texto + CEP)',
                        'whatsapp' => 'WhatsApp (Texto Central)',
                    ])->default('default'),
                Forms\Components\TextInput::make('badge')->label('Texto do Badge'),
                Forms\Components\Repeater::make('slides')
                    ->schema([
                        Forms\Components\TextInput::make('title')->required(),
                        Forms\Components\Textarea::make('subtitle'),
                        Forms\Components\FileUpload::make('image')->image()->directory('hero'),
                    ])->minItems(1),
                Forms\Components\Repeater::make('stats')
                    ->schema([
                        Forms\Components\TextInput::make('value')->required(),
                        Forms\Components\TextInput::make('label')->required(),
                    ])->columns(2),
            ]);
    }

    protected static function getPartnersBlock(): Forms\Components\Builder\Block
    {
        return Forms\Components\Builder\Block::make('partners')
            ->label(__('Parceiros'))
            ->icon('heroicon-o-building-office')
            ->schema([
                Forms\Components\TextInput::make('title')->label('Título'),
                Forms\Components\Textarea::make('description')->label('Descrição'),
                Forms\Components\Repeater::make('items')
                    ->schema([
                        Forms\Components\TextInput::make('name')->required(),
                        Forms\Components\TextInput::make('icon')->label('Ícone (Lucide)'),
                    ])->columns(2),
            ]);
    }

    protected static function getServicesBlock(): Forms\Components\Builder\Block
    {
        return Forms\Components\Builder\Block::make('services')
            ->label(__('Serviços'))
            ->icon('heroicon-o-wrench-screwdriver')
            ->schema([
                Forms\Components\TextInput::make('title')->label('Título'),
                Forms\Components\Textarea::make('description')->label('Descrição'),
                Forms\Components\Repeater::make('items')
                    ->schema([
                        Forms\Components\TextInput::make('title')->required(),
                        Forms\Components\Textarea::make('description'),
                        Forms\Components\TextInput::make('icon'),
                        Forms\Components\TagsInput::make('bullets'),
                        Forms\Components\TextInput::make('cta_label'),
                        Forms\Components\TextInput::make('cta_url'),
                    ]),
            ]);
    }

    protected static function getTimelineBlock(): Forms\Components\Builder\Block
    {
        return Forms\Components\Builder\Block::make('timeline')
            ->label(__('Linha do Tempo (Etapas)'))
            ->icon('heroicon-o-clock')
            ->schema([
                Forms\Components\TextInput::make('title')->label('Título'),
                Forms\Components\Repeater::make('steps')
                    ->schema([
                        Forms\Components\TextInput::make('step_label')->required(),
                        Forms\Components\TextInput::make('title')->required(),
                        Forms\Components\Textarea::make('description'),
                        Forms\Components\TextInput::make('icon'),
                    ]),
            ]);
    }

    protected static function getShowcaseBlock(): Forms\Components\Builder\Block
    {
        return Forms\Components\Builder\Block::make('showcase')
            ->label(__('Galeria de Obras (Showcase)'))
            ->icon('heroicon-o-camera')
            ->schema([
                Forms\Components\TextInput::make('badge')->label('Texto do Badge (Laranja)')->placeholder('NOSSAS OBRAS'),
                Forms\Components\TextInput::make('title')->label('Título')->required(),
                Forms\Components\Textarea::make('description')->label('Descrição'),
                Forms\Components\TextInput::make('limit')->numeric()->default(4)->label('Limite de itens'),
            ]);
    }

    protected static function getFaqBlock(): Forms\Components\Builder\Block
    {
        return Forms\Components\Builder\Block::make('faq')
            ->label(__('FAQ (Perguntas Frequentes)'))
            ->icon('heroicon-o-question-mark-circle')
            ->schema([
                Forms\Components\TextInput::make('title')->label('Título'),
                Forms\Components\Repeater::make('items')
                    ->schema([
                        Forms\Components\TextInput::make('question')->required(),
                        Forms\Components\Textarea::make('answer')->required(),
                    ]),
            ]);
    }

    protected static function getTestimonialsBlock(): Forms\Components\Builder\Block
    {
        return Forms\Components\Builder\Block::make('testimonials')
            ->label(__('Depoimentos'))
            ->icon('heroicon-o-chat-bubble-bottom-center-text')
            ->schema([
                Forms\Components\TextInput::make('title')->label('Título'),
                Forms\Components\Repeater::make('items')
                    ->schema([
                        Forms\Components\Textarea::make('quote')->required(),
                        Forms\Components\TextInput::make('author_name')->required(),
                        Forms\Components\TextInput::make('author_role'),
                        Forms\Components\TextInput::make('stars')->numeric()->default(5),
                    ]),
            ]);
    }

    protected static function getCoverageBlock(): Forms\Components\Builder\Block
    {
        return Forms\Components\Builder\Block::make('coverage')
            ->label(__('Área de Atendimento'))
            ->icon('heroicon-o-map-pin')
            ->schema([
                Forms\Components\TextInput::make('title')->label('Título'),
                Forms\Components\Select::make('cities')
                    ->label('Cidades Atendidas')
                    ->multiple()
                    ->options(\App\Models\OperationArea::query()->where('is_active', true)->pluck('city', 'city'))
                    ->helperText('Selecione as cidades que deseja destacar. Os dados vêm do módulo de Áreas de Operação.'),
            ]);
    }

    protected static function getCtaBlock(): Forms\Components\Builder\Block
    {
        return Forms\Components\Builder\Block::make('cta')
            ->label(__('Chamada para Ação (CTA)'))
            ->icon('heroicon-o-megaphone')
            ->schema([
                Forms\Components\TextInput::make('title'),
                Forms\Components\Textarea::make('subtitle'),
                Forms\Components\TextInput::make('button_label'),
                Forms\Components\TextInput::make('button_url'),
            ]);
    }

    protected static function getDifferentialsBlock(): Forms\Components\Builder\Block
    {
        return Forms\Components\Builder\Block::make('differentials')
            ->label(__('Diferenciais (Pilar / Missão / Visão)'))
            ->icon('heroicon-o-shield-check')
            ->schema([
                Forms\Components\TextInput::make('subtitle')->label('Subtítulo'),
                Forms\Components\TextInput::make('title')->label('Título Principal'),
                Forms\Components\Textarea::make('description')->label('Descrição / Texto de Apoio'),
                Forms\Components\Repeater::make('items')
                    ->label('Itens (Recomendado: 3)')
                    ->schema([
                        Forms\Components\TextInput::make('title')->label('Título')->required(),
                        Forms\Components\Textarea::make('description')->label('Descrição')->required(),
                        Forms\Components\TextInput::make('icon')->label('Ícone (Lucide)')->default('check-circle'),
                    ])->columns(2),
            ]);
    }

    protected static function getPageHeaderBlock(): Forms\Components\Builder\Block
    {
        return Forms\Components\Builder\Block::make('page_header')
            ->label(__('Cabeçalho da Página'))
            ->icon('heroicon-o-document-text')
            ->schema([
                Forms\Components\TextInput::make('badge')->label('Texto de Apoio (Laranja)')->placeholder('NOSSOS SERVIÇOS'),
                Forms\Components\TextInput::make('title')->label('Título Principal')->required(),
                Forms\Components\Textarea::make('description')->label('Descrição'),
                Forms\Components\Toggle::make('show_breadcrumbs')->label('Mostrar Breadcrumbs')->default(true),
                Forms\Components\FileUpload::make('background_image')->image()->directory('headers')->label('Imagem de Fundo (Opcional)'),
            ]);
    }

    protected static function getContactFormBlock(): Forms\Components\Builder\Block
    {
        return Forms\Components\Builder\Block::make('contact_form')
            ->label(__('Formulário de Contato'))
            ->icon('heroicon-o-envelope')
            ->schema([
                Forms\Components\TextInput::make('title')->label('Título')->default('Entre em Contato'),
                Forms\Components\Textarea::make('description')->label('Descrição'),
                Forms\Components\TextInput::make('email_to')->label('Enviar para (e-mail)')->placeholder('contato@empresa.com'),
            ]);
    }

    protected static function getMapBlock(): Forms\Components\Builder\Block
    {
        return Forms\Components\Builder\Block::make('map')
            ->label(__('Mapa (Google Maps)'))
            ->icon('heroicon-o-map')
            ->schema([
                Forms\Components\TextInput::make('title')->label('Título'),
                Forms\Components\Textarea::make('iframe_code')
                    ->label('Código de Incorporação (iframe)')
                    ->helperText('Cole aqui o <iframe> gerado pelo Google Maps'),
            ]);
    }

    protected static function getRichTextBlock(): Forms\Components\Builder\Block
    {
        return Forms\Components\Builder\Block::make('rich_text')
            ->label(__('Texto Livre (Editor)'))
            ->icon('heroicon-o-document-text')
            ->schema([
                Forms\Components\RichEditor::make('content')->required(),
            ]);
    }

    protected static function getCalculatorBlock(): Forms\Components\Builder\Block
    {
        return Forms\Components\Builder\Block::make('calculator')
            ->label(__('Calculadora de Concreto'))
            ->icon('heroicon-o-calculator')
            ->schema([
                Forms\Components\TextInput::make('title')->label('Título')->default('Calculadora de Volume'),
            ]);
    }

    protected static function getBudgetFormBlock(): Forms\Components\Builder\Block
    {
        return Forms\Components\Builder\Block::make('budget_form')
            ->label(__('Formulário de Orçamento (Wizard)'))
            ->icon('heroicon-o-document-text')
            ->schema([
                Forms\Components\TextInput::make('title')->label('Título')->default('Solicitar Orçamento'),
                Forms\Components\Textarea::make('description')->label('Descrição'),
            ]);
    }
    protected static function getContactBannerBlock(): Forms\Components\Builder\Block
    {
        return Forms\Components\Builder\Block::make('contact_banner')
            ->label(__('Banner de Atendimento (Call Actions)'))
            ->icon('heroicon-o-chat-bubble-left-right')
            ->schema([
                Forms\Components\TextInput::make('badge')
                    ->label('Badge (Texto Superior)')
                    ->default('ATENDIMENTO'),
                Forms\Components\TextInput::make('title')
                    ->label('Título')
                    ->default('Fale conosco')
                    ->required(),
                Forms\Components\Textarea::make('description')
                    ->label('Descrição')
                    ->default('Dúvidas, orçamento ou suporte: estamos prontos para atender você por telefone, WhatsApp ou e-mail.')
                    ->rows(2),
                Forms\Components\Grid::make(3)
                    ->schema([
                        Forms\Components\Toggle::make('whatsapp_enabled')
                            ->label('Botão WhatsApp')
                            ->default(true),
                        Forms\Components\Toggle::make('call_enabled')
                            ->label('Botão Ligar')
                            ->default(true),
                        Forms\Components\Toggle::make('email_enabled')
                            ->label('Botão E-mail')
                            ->default(true),
                    ]),
            ]);
    }

    protected static function getModuleReferenceBlock(): Forms\Components\Builder\Block
    {
        return Forms\Components\Builder\Block::make('module_reference')
            ->label(__('Módulo Global (Seção Pronta)'))
            ->icon('heroicon-o-squares-plus')
            ->schema([
                Forms\Components\Select::make('content_section_id')
                    ->label(__('Seção de Conteúdo'))
                    ->options(\App\Models\ContentSection::query()->pluck('name', 'id'))
                    ->required()
                    ->searchable()
                    ->helperText(__('Selecione uma seção criada no módulo "Seções do site" para reutilizá-la aqui.')),
            ]);
    }
}
