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
        return __('Nossas Obras');
    }

    public static function getModelLabel(): string
    {
        return __('Obra');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Nossas Obras');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('Detalhes da Obra'))
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->label(__('Título'))
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('location')
                            ->label(__('Localização'))
                            ->placeholder(__('Ex: São Paulo, SP'))
                            ->maxLength(255),
                        Forms\Components\Textarea::make('description')
                            ->label(__('Descrição'))
                            ->rows(3)
                            ->maxLength(65535)
                            ->columnSpanFull(),
                        Forms\Components\FileUpload::make('images')
                            ->label(__('Galeria de Fotos'))
                            ->image()
                            ->multiple()
                            ->reorderable()
                            ->imageEditor()
                            ->directory('showcases')
                            ->columnSpanFull(),
                        Forms\Components\Repeater::make('videos')
                            ->label(__('Vídeos'))
                            ->schema([
                                Forms\Components\TextInput::make('title')
                                    ->label(__('Título do Vídeo'))
                                    ->placeholder(__('Ex: Drone da Obra')),
                                Forms\Components\TextInput::make('url')
                                    ->label(__('URL do Vídeo (YouTube, Vimeo ou .mp4)'))
                                    ->required()
                                    ->url()
                                    ->placeholder(__('https://www.youtube.com/watch?v=...')),
                            ])
                            ->collapsible()
                            ->itemLabel(fn (array $state): ?string => $state['title'] ?? null)
                            ->columnSpanFull(),
                        Forms\Components\Toggle::make('is_active')
                            ->label(__('Ativo'))
                            ->default(true),
                        Forms\Components\TextInput::make('sort_order')
                            ->label(__('Ordem'))
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
                    ->label(__('Foto'))
                    ->circular()
                    ->limit(1)
                    ->stacked(),
                Tables\Columns\TextColumn::make('title')
                    ->label(__('Título'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('location')
                    ->label(__('Localização'))
                    ->searchable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label(__('Ativo'))
                    ->boolean(),
                Tables\Columns\TextColumn::make('sort_order')
                    ->label(__('Ordem'))
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('Criado em'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label(__('Status'))
                    ->boolean()
                    ->trueLabel(__('Ativo'))
                    ->falseLabel(__('Inativo'))
                    ->native(false),
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
