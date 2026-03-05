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

    protected static ?string $navigationIcon = 'heroicon-o-squares-2x2';

    protected static ?string $navigationLabel = 'Seções do site';

    protected static ?string $modelLabel = 'Seção';

    protected static ?string $pluralModelLabel = 'Seções';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Identificação')
                    ->schema([
                        Forms\Components\Select::make('type')
                            ->label('Tipo da seção')
                            ->options(ContentSection::typeLabels())
                            ->required()
                            ->live()
                            ->afterStateUpdated(function (Forms\Set $set, ?string $state) {
                                if ($state) {
                                    $set('slug', ContentSection::slugForType($state));
                                    $set('name', ContentSection::typeLabels()[$state] ?? $state);
                                    if ($state === ContentSection::TYPE_HERO) {
                                        $set('slug', 'hero');
                                    }
                                }
                            }),
                        Forms\Components\TextInput::make('slug')
                            ->label('Slug (identificador único)')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255)
                            ->helperText('Usado no código para buscar os dados. Ex: faq, partners'),
                        Forms\Components\TextInput::make('name')
                            ->label('Nome (admin)')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Toggle::make('is_active')
                            ->label('Ativo')
                            ->default(true)
                            ->helperText('Se inativo, a seção não aparece no site e usa o conteúdo estático.'),
                        Forms\Components\TextInput::make('sort_order')
                            ->label('Ordem')
                            ->numeric()
                            ->minValue(0),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Cabeçalho (opcional)')
                    ->description('Subtítulo, título e descrição exibidos no topo da seção.')
                    ->schema([
                        Forms\Components\TextInput::make('content.header.subtitle')
                            ->label('Subtítulo')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('content.header.title')
                            ->label('Título')
                            ->maxLength(255),
                        Forms\Components\Textarea::make('content.header.description')
                            ->label('Descrição')
                            ->rows(2),
                    ])
                    ->columns(1)
                    ->collapsible(),

                // Hero (página inicial — a ferramenta de CEP sempre aparece na hero)
                Forms\Components\Section::make('Hero — Slideshow e layout')
                    ->description('Várias heroes podem existir; apenas a ativa (marcada como Ativo) é exibida. A ferramenta de CEP fica sempre visível.')
                    ->schema([
                        Forms\Components\Select::make('content.layout')
                            ->label('Layout')
                            ->options([
                                ContentSection::HERO_LAYOUT_DEFAULT => 'Padrão (texto à esquerda + CEP à direita)',
                                ContentSection::HERO_LAYOUT_WHATSAPP => 'WhatsApp (destaque central + CEP abaixo)',
                            ])
                            ->default(ContentSection::HERO_LAYOUT_DEFAULT),
                        Forms\Components\TextInput::make('content.badge')
                            ->label('Texto do badge')
                            ->placeholder('Qualidade Certificada')
                            ->maxLength(255),
                        Forms\Components\Repeater::make('content.slideshow')
                            ->label('Slides (título e subtítulo; o primeiro é exibido na hero)')
                            ->schema([
                                Forms\Components\TextInput::make('title')->label('Título')->required()->columnSpanFull(),
                                Forms\Components\Textarea::make('subtitle')->label('Subtítulo')->rows(2)->columnSpanFull(),
                                Forms\Components\FileUpload::make('image')
                                    ->label('Imagem (opcional)')
                                    ->image()
                                    ->directory('hero-slideshow'),
                            ])
                            ->columns(1)
                            ->itemLabel(fn (array $state): ?string => $state['title'] ?? 'Slide')
                            ->defaultItems(1),
                        Forms\Components\Repeater::make('content.stats')
                            ->label('Números (opcional; ex: 500+ Obras)')
                            ->schema([
                                Forms\Components\TextInput::make('value')->label('Valor')->placeholder('500+'),
                                Forms\Components\TextInput::make('label')->label('Descrição')->placeholder('Obras atendidas'),
                            ])
                            ->columns(2)
                            ->itemLabel(fn (array $state): ?string => ($state['value'] ?? '') . ' ' . ($state['label'] ?? ''))
                            ->maxItems(6),
                    ])
                    ->visible(fn ($get): bool => $get('type') === ContentSection::TYPE_HERO)
                    ->collapsible(),

                // FAQ
                Forms\Components\Section::make('Perguntas e respostas')
                    ->schema([
                        Forms\Components\Repeater::make('content.items')
                            ->label('Itens FAQ')
                            ->schema([
                                Forms\Components\TextInput::make('question')->label('Pergunta')->required(),
                                Forms\Components\Textarea::make('answer')->label('Resposta')->required()->rows(2),
                            ])
                            ->columns(1)
                            ->itemLabel(fn (array $state): ?string => $state['question'] ?? null),
                    ])
                    ->visible(fn ($get): bool => $get('type') === ContentSection::TYPE_FAQ)
                    ->collapsible(),

                // Partners
                Forms\Components\Section::make('Parceiros')
                    ->schema([
                        Forms\Components\Repeater::make('content.items')
                            ->label('Empresas / Parceiros')
                            ->schema([
                                Forms\Components\TextInput::make('name')->label('Nome')->required(),
                                Forms\Components\TextInput::make('icon')
                                    ->label('Ícone (Lucide)')
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
                    ->schema([
                        Forms\Components\Repeater::make('content.items')
                            ->label('Serviços')
                            ->schema([
                                Forms\Components\TextInput::make('title')->label('Título')->required(),
                                Forms\Components\TextInput::make('subtitle')->label('Subtítulo (ex: por m³)'),
                                Forms\Components\Textarea::make('description')->label('Descrição')->rows(2),
                                Forms\Components\TextInput::make('icon')->label('Ícone Lucide')->placeholder('droplets, gauge, wrench'),
                                Forms\Components\TagsInput::make('bullets')->label('Lista de itens')->placeholder('Item'),
                                Forms\Components\TextInput::make('cta_label')->label('Texto do link')->default('Solicitar Orçamento'),
                                Forms\Components\TextInput::make('cta_url')->label('URL do link')->default('#orcamento'),
                            ])
                            ->columns(1)
                            ->itemLabel(fn (array $state): ?string => $state['title'] ?? null),
                    ])
                    ->visible(fn ($get): bool => $get('type') === ContentSection::TYPE_SERVICES)
                    ->collapsible(),

                // Testimonials
                Forms\Components\Section::make('Depoimentos')
                    ->schema([
                        Forms\Components\Repeater::make('content.items')
                            ->label('Depoimentos')
                            ->schema([
                                Forms\Components\Textarea::make('quote')->label('Citação')->required()->rows(3),
                                Forms\Components\TextInput::make('author_name')->label('Nome do autor')->required(),
                                Forms\Components\TextInput::make('author_role')->label('Cargo / Obra'),
                                Forms\Components\TextInput::make('stars')->label('Estrelas (1-5)')->numeric()->minValue(1)->maxValue(5)->default(5),
                            ])
                            ->columns(1)
                            ->itemLabel(fn (array $state): ?string => $state['author_name'] ?? null),
                    ])
                    ->visible(fn ($get): bool => $get('type') === ContentSection::TYPE_TESTIMONIALS)
                    ->collapsible(),

                // Coverage
                Forms\Components\Section::make('Onde atuamos')
                    ->schema([
                        Forms\Components\Repeater::make('content.cities')
                            ->label('Cidades / Regiões')
                            ->schema([
                                Forms\Components\TextInput::make('label')->label('Nome')->required(),
                            ])
                            ->itemLabel(fn (array $state): ?string => $state['label'] ?? null),
                        Forms\Components\Repeater::make('content.sidebar')
                            ->label('Cards laterais')
                            ->schema([
                                Forms\Components\TextInput::make('title')->label('Título')->required(),
                                Forms\Components\Textarea::make('description')->label('Descrição')->rows(2),
                            ])
                            ->columns(1)
                            ->itemLabel(fn (array $state): ?string => $state['title'] ?? null),
                    ])
                    ->visible(fn ($get): bool => $get('type') === ContentSection::TYPE_COVERAGE)
                    ->collapsible(),

                // Differentials
                Forms\Components\Section::make('Diferenciais')
                    ->schema([
                        Forms\Components\Repeater::make('content.items')
                            ->label('Itens')
                            ->schema([
                                Forms\Components\TextInput::make('icon')->label('Ícone Lucide')->placeholder('clock, microscope'),
                                Forms\Components\TextInput::make('title')->label('Título')->required(),
                                Forms\Components\Textarea::make('description')->label('Descrição')->rows(2),
                            ])
                            ->columns(1)
                            ->itemLabel(fn (array $state): ?string => $state['title'] ?? null),
                    ])
                    ->visible(fn ($get): bool => $get('type') === ContentSection::TYPE_DIFFERENTIALS)
                    ->collapsible(),

                // Timeline
                Forms\Components\Section::make('Etapas (Como funciona)')
                    ->schema([
                        Forms\Components\Repeater::make('content.steps')
                            ->label('Etapas')
                            ->schema([
                                Forms\Components\TextInput::make('step_label')->label('Rótulo (ex: Etapa 1)')->required(),
                                Forms\Components\TextInput::make('title')->label('Título')->required(),
                                Forms\Components\Textarea::make('description')->label('Descrição')->rows(2),
                                Forms\Components\TextInput::make('icon')->label('Ícone Lucide')->placeholder('message-square-text, truck'),
                            ])
                            ->columns(1)
                            ->itemLabel(fn (array $state): ?string => $state['title'] ?? null),
                    ])
                    ->visible(fn ($get): bool => $get('type') === ContentSection::TYPE_TIMELINE)
                    ->collapsible(),

                // CTA Contact
                Forms\Components\Section::make('CTA Contato')
                    ->schema([
                        Forms\Components\TextInput::make('content.title')
                            ->label('Título')
                            ->maxLength(255),
                        Forms\Components\Textarea::make('content.subtitle')
                            ->label('Subtítulo')
                            ->rows(2),
                    ])
                    ->visible(fn ($get): bool => $get('type') === ContentSection::TYPE_CTA_CONTACT)
                    ->collapsible(),
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
