<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ShowcaseResource\Pages;
use App\Filament\Resources\ShowcaseResource\RelationManagers;
use App\Models\Showcase;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ShowcaseResource extends Resource
{
    protected static ?string $model = Showcase::class;

    protected static ?string $navigationIcon = 'heroicon-o-camera';

    protected static ?string $navigationGroup = 'Website';

    public static function getNavigationLabel(): string
    {
        return 'Nossas Obras';
    }

    public static function getModelLabel(): string
    {
        return 'Obra';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Nossas Obras';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Detalhes da Obra')
                    ->description('Cadastre as informações, fotos e vídeos das obras concluídas para o seu portfólio.')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->label('Título da Obra')
                            ->placeholder('Ex: Residencial Alphaville, Pavimentação BR-101...')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('location')
                            ->label('Localização')
                            ->placeholder('Ex: São Paulo, SP')
                            ->maxLength(255),
                        Forms\Components\Textarea::make('description')
                            ->label('Descrição')
                            ->helperText('Conte um pouco sobre o desafio e o resultado da obra.')
                            ->rows(3)
                            ->maxLength(65535)
                            ->columnSpanFull(),
                        Forms\Components\FileUpload::make('images')
                            ->label('Galeria de Fotos')
                            ->helperText('Arraste as fotos para mudar a ordem de exibição.')
                            ->image()
                            ->multiple()
                            ->reorderable()
                            ->imageEditor()
                            ->directory('showcases')
                            ->columnSpanFull(),
                        Forms\Components\Repeater::make('videos')
                            ->label('Vídeos da Obra')
                            ->schema([
                                Forms\Components\TextInput::make('title')
                                    ->label('Título do Vídeo')
                                    ->placeholder('Ex: Drone da Obra, Depoimento do Cliente'),
                                Forms\Components\TextInput::make('url')
                                    ->label('URL do Vídeo (YouTube, Vimeo ou .mp4)')
                                    ->required()
                                    ->url()
                                    ->placeholder('https://www.youtube.com/watch?v=...'),
                            ])
                            ->collapsible()
                            ->itemLabel(fn (array $state): ?string => $state['title'] ?? null)
                            ->columnSpanFull(),
                        Forms\Components\Toggle::make('is_active')
                            ->label('Exibir no Site')
                            ->default(true),
                        Forms\Components\TextInput::make('sort_order')
                            ->label('Ordem de Exibição')
                            ->numeric()
                            ->default(0),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('images')
                    ->label('Foto')
                    ->circular()
                    ->limit(1)
                    ->stacked(),
                Tables\Columns\TextColumn::make('title')
                    ->label('Título')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('location')
                    ->label('Localização')
                    ->searchable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Ativo')
                    ->boolean(),
                Tables\Columns\TextColumn::make('sort_order')
                    ->label('Ordem')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Criado em')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Status')
                    ->boolean()
                    ->trueLabel('Ativo')
                    ->falseLabel('Inativo')
                    ->native(false),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label('Editar'),
                Tables\Actions\DeleteAction::make()
                    ->label('Excluir'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('Excluir Selecionados'),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListShowcases::route('/'),
            'create' => Pages\CreateShowcase::route('/create'),
            'edit' => Pages\EditShowcase::route('/{record}/edit'),
        ];
    }
}
