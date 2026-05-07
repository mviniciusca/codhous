<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BackgroundImageResource\Pages;
use App\Models\BackgroundImage;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class BackgroundImageResource extends Resource
{
    protected static ?string $model = BackgroundImage::class;

    protected static ?string $navigationIcon = 'heroicon-o-photo';

    protected static ?string $navigationGroup = 'Comunicação';

    protected static ?int $navigationSort = 10;

    public static function getNavigationLabel(): string
    {
        return 'Galeria de Fundos';
    }

    public static function getModelLabel(): string
    {
        return 'Fundo';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Galeria de Fundos';
    }

    // ─── Form ─────────────────────────────────────────────────

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Imagem de Fundo')
                    ->description('Faça upload de uma imagem para usar como fundo no Gerador.IA.')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nome')
                            ->placeholder('Ex: Pôr do Sol na Praia, Cidade à Noite...')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('category')
                            ->label('Categoria')
                            ->placeholder('Ex: Natureza, Urbano, Abstrato...')
                            ->maxLength(100),

                        Forms\Components\SpatieMediaLibraryFileUpload::make('image')
                            ->label('Imagem')
                            ->collection('image')
                            ->image()
                            ->imageEditor()
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                            ->maxSize(10240) // 10MB
                            ->helperText('JPG, PNG ou WebP. Tamanho recomendado: 1080×1080px.')
                            ->columnSpanFull(),

                        Forms\Components\TextInput::make('sort_order')
                            ->label('Ordem')
                            ->helperText('Menor número aparece primeiro na galeria do criador.')
                            ->numeric()
                            ->default(0),

                        Forms\Components\Toggle::make('is_active')
                            ->label('Visível no Criador')
                            ->default(true),
                    ])->columns(2),
            ]);
    }

    // ─── Table ────────────────────────────────────────────────

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\SpatieMediaLibraryImageColumn::make('image')
                    ->label('Thumb')
                    ->collection('image')
                    ->conversion('thumb')
                    ->width(60)
                    ->height(60)
                    ->extraImgAttributes(['class' => 'rounded-lg object-cover']),

                Tables\Columns\TextColumn::make('name')
                    ->label('Nome')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('category')
                    ->label('Categoria')
                    ->badge()
                    ->color('info')
                    ->searchable(),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Visível')
                    ->boolean(),

                Tables\Columns\TextColumn::make('sort_order')
                    ->label('Ordem')
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Adicionado')
                    ->dateTime('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Visibilidade')
                    ->boolean()
                    ->trueLabel('Visíveis')
                    ->falseLabel('Ocultos')
                    ->native(false),

                Tables\Filters\SelectFilter::make('category')
                    ->label('Categoria')
                    ->options(
                        fn () => BackgroundImage::query()
                            ->whereNotNull('category')
                            ->distinct()
                            ->pluck('category', 'category')
                            ->toArray()
                    ),
            ])
            ->actions([
                Tables\Actions\EditAction::make()->label('Editar'),
                Tables\Actions\DeleteAction::make()->label('Excluir'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()->label('Excluir Selecionados'),

                    Tables\Actions\BulkAction::make('toggleActive')
                        ->label('Alternar Visibilidade')
                        ->icon('heroicon-o-eye')
                        ->action(function ($records) {
                            foreach ($records as $record) {
                                $record->update(['is_active' => ! $record->is_active]);
                            }
                        })
                        ->deselectRecordsAfterCompletion(),
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
            'index'  => Pages\ListBackgroundImages::route('/'),
            'create' => Pages\CreateBackgroundImage::route('/create'),
            'edit'   => Pages\EditBackgroundImage::route('/{record}/edit'),
        ];
    }
}
