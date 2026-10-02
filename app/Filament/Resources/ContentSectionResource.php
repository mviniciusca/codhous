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
                Forms\Components\Tabs::make('Tabs')
                    ->tabs([
                        Forms\Components\Tabs\Tab::make('Conteúdo Principal')
                            ->icon('heroicon-o-document-text')
                            ->schema([


                Forms\Components\Section::make('Cabeçalho (opcional)')
                    ->icon('heroicon-o-bars-3-bottom-left')
                    ->description('Subtítulo, título e descrição exibidos no topo da seção.')
                    ->schema([
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
                            ->rows(2),
                    ])
                    ->columns(1)
                    ->collapsible(),

                // Hero
                Forms\Components\Section::make('Hero Section')
                    ->description('Destaque principal no topo da página.')
                    ->icon('heroicon-o-presentation-chart-line')
                    ->schema([
                        Forms\Components\Select::make('content.layout')
                            ->label('Layout')
                            ->helperText('Escolha o estilo de exibição.')
                            ->options([
                                'default' => 'Padrão (Texto + CEP)',
                                'whatsapp' => 'WhatsApp (Texto Central)',
                            ])->default('default'),
                        Forms\Components\Grid::make(2)->schema([
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
                        ]),
                        Forms\Components\Select::make('content.image_alignment')
                            ->label('Alinhamento da Imagem / Vídeo')
                            ->options([
                                'center' => 'Centro',
                                'top' => 'Topo',
                                'bottom' => 'Base',
                            ])->default('center'),
                        Forms\Components\Fieldset::make('Botões de Ação')
                            ->schema([
                                Forms\Components\Group::make([
                                    Forms\Components\TextInput::make('content.primary_button_text')->label('Texto Botão 1')->helperText('Cor principal'),
                                    Forms\Components\TextInput::make('content.primary_button_url')->label('Link Botão 1'),
                                    Forms\Components\ToggleButtons::make('content.primary_button_icon_select')
                                        ->label('Ícone rápido')
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
                                        ->visible(fn ($get) => $get('content.primary_button_icon_select') === 'outro'),
                                ])->columns(1),
                                Forms\Components\Group::make([
                                    Forms\Components\TextInput::make('content.secondary_button_text')->label('Texto Botão 2')->helperText('Borda principal (vazado)'),
                                    Forms\Components\TextInput::make('content.secondary_button_url')->label('Link Botão 2'),
                                    Forms\Components\ToggleButtons::make('content.secondary_button_icon_select')
                                        ->label('Ícone rápido')
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
                                        ->visible(fn ($get) => $get('content.secondary_button_icon_select') === 'outro'),
                                ])->columns(1),
                            ]),
                        Forms\Components\Repeater::make('content.stats')
                            ->label('Estatísticas')
                            ->helperText('Números importantes em destaque (ex: +500 Projetos).')
                            ->schema([
                                Forms\Components\TextInput::make('value')->label('Valor')->helperText('Ex: +500')->required(),
                                Forms\Components\TextInput::make('label')->label('Rótulo')->helperText('Ex: Projetos Entregues')->required(),
                            ])
                            ->columns(2)
                            ->collapsible()
                            ->cloneable()
                            ->itemLabel(fn (array $state): ?string => trim(($state['value'] ?? '') . ' ' . ($state['label'] ?? '')) ?: null)
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

                // Partners
                Forms\Components\Section::make('Parceiros')
                    ->description('Adicione as logomarcas ou nomes das empresas parceiras.')
                    ->icon('heroicon-o-building-office-2')
                    ->schema([
                        Forms\Components\Repeater::make('content.items')
                            ->label('Empresas / Parceiros')
                            ->schema([
                                Forms\Components\TextInput::make('name')->label('Nome')->helperText('Nome do parceiro.')->required(),
                                Forms\Components\TextInput::make('icon')
                                    ->label('Ícone (Lucide)')
                                    ->helperText('Nome do ícone correspondente.')
                                    ->placeholder('building-2, hard-hat, factory...')
                                    ->maxLength(50),
                            ])
                            ->columns(2)
                            ->itemLabel(fn (array $state): ?string => $state['name'] ?? null),
                    ])
                    ->visible(fn ($get): bool => $get('type') === ContentSection::TYPE_PARTNERS)
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
                                Forms\Components\Textarea::make('description')->label('Descrição')->helperText('Detalhes do serviço.')->rows(2),
                                Forms\Components\TextInput::make('icon')->label('Ícone Lucide')->helperText('Ícone do serviço.')->placeholder('droplets, gauge, wrench'),
                                Forms\Components\TagsInput::make('bullets')->label('Lista de itens')->helperText('Tags ou tópicos do serviço.')->placeholder('Item'),
                                Forms\Components\TextInput::make('cta_label')->label('Texto do link')->helperText('O que vai escrito no botão.')->default('Solicitar Orçamento'),
                                Forms\Components\TextInput::make('cta_url')->label('URL do link')->helperText('Para onde o botão leva.')->default('#orcamento'),
                            ])
                            ->columns(1)
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
                                Forms\Components\Textarea::make('quote')->label('Citação')->helperText('O texto do depoimento.')->required()->rows(3),
                                Forms\Components\TextInput::make('author_name')->label('Nome do autor')->helperText('Pessoa que deu o depoimento.')->required(),
                                Forms\Components\TextInput::make('author_role')->label('Cargo / Obra')->helperText('Ex: Cliente Codhous'),
                                Forms\Components\TextInput::make('stars')->label('Estrelas (1-5)')->helperText('Nota de 1 a 5.')->numeric()->minValue(1)->maxValue(5)->default(5),
                            ])
                            ->columns(1)
                            ->itemLabel(fn (array $state): ?string => $state['author_name'] ?? null),
                    ])
                    ->visible(fn ($get): bool => $get('type') === ContentSection::TYPE_TESTIMONIALS)
                    ->collapsible(),

                // Coverage
                Forms\Components\Section::make('Onde atuamos')
                    ->description('Selecione as cidades e configure os cards informativos.')
                    ->icon('heroicon-o-map-pin')
                    ->schema([
                        Forms\Components\Select::make('content.cities')
                            ->label('Cidades Atendidas')
                            ->multiple()
                            ->options(\App\Models\OperationArea::query()->where('is_active', true)->pluck('city', 'city'))
                            ->helperText('Selecione as cidades que deseja destacar. Os dados vêm do módulo de Áreas de Operação.'),
                        Forms\Components\Repeater::make('content.sidebar')
                            ->label('Cards laterais')
                            ->schema([
                                Forms\Components\TextInput::make('title')->label('Título')->helperText('Título do card.')->required(),
                                Forms\Components\Textarea::make('description')->label('Descrição')->helperText('Descrição do card.')->rows(2),
                            ])
                            ->columns(1)
                            ->itemLabel(fn (array $state): ?string => $state['title'] ?? null),
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
                                Forms\Components\Toggle::make('content.budget_btn_enabled')
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
                                Forms\Components\Toggle::make('content.whatsapp_btn_enabled')
                                    ->label('Mostrar WhatsApp')
                                    ->helperText('Puxa o número das configurações do site.')
                                    ->onIcon('heroicon-m-check')
                                    ->default(true),
                                Forms\Components\Toggle::make('content.email_btn_enabled')
                                    ->label('Mostrar E-mail')
                                    ->helperText('Mostra o e-mail cadastrado acima.')
                                    ->onIcon('heroicon-m-check')
                                    ->default(true),
                                Forms\Components\Toggle::make('content.phone_btn_enabled')
                                    ->label('Mostrar Telefone')
                                    ->helperText('Puxa do cadastro da empresa.')
                                    ->onIcon('heroicon-m-check')
                                    ->default(true),
                                Forms\Components\Toggle::make('content.address_enabled')
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
                                Forms\Components\Toggle::make('content.whatsapp_enabled')
                                    ->label('Botão WhatsApp')
                                    ->helperText('Ativa o botão do WhatsApp.')
                                    ->onIcon('heroicon-m-check')
                                    ->default(true),
                                Forms\Components\Toggle::make('content.call_enabled')
                                    ->label('Botão Ligar')
                                    ->helperText('Ativa o botão de ligação.')
                                    ->onIcon('heroicon-m-check')
                                    ->default(true),
                                Forms\Components\Toggle::make('content.email_enabled')
                                    ->label('Botão E-mail')
                                    ->helperText('Ativa o botão de e-mail.')
                                    ->onIcon('heroicon-m-check')
                                    ->default(true),
                            ]),
                    ])
                    ->visible(fn ($get): bool => $get('type') === ContentSection::TYPE_CONTACT_BANNER)
                    ->collapsible(),
                            ]),

                        Forms\Components\Tabs\Tab::make('Configurações')
                            ->icon('heroicon-o-cog-6-tooth')
                            ->schema([
                                Forms\Components\Section::make('Identificação')
                                    ->description('Informações básicas de identificação e configuração da seção no sistema.')
                                    ->icon('heroicon-o-identification')
                                    ->schema([
                                        Forms\Components\Select::make('type')
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
                                        Forms\Components\Toggle::make('is_active')
                                            ->label('Ativo')
                                            ->onIcon('heroicon-m-check')
                                            ->default(true)
                                            ->helperText('Se inativo, a seção não aparece no site e usa o conteúdo estático.'),
                                        Forms\Components\TextInput::make('sort_order')
                                            ->label('Ordem')
                                            ->numeric()
                                            ->helperText('Ordem na listagem.')
                                            ->minValue(0),
                                    ])
                                    ->columns(2),
                            ]),
                    ])
                    ->columnSpanFull(),
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
