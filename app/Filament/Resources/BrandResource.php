<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BrandResource\Pages;
use App\Models\Brand;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class BrandResource extends Resource
{
    protected static ?string $model = Brand::class;

    protected static ?string $navigationGroup = 'Website';
    
    protected static ?string $navigationIcon = 'heroicon-o-star';

    protected static ?int $navigationSort = 2;
    
    protected static ?string $navigationLabel = 'Marcas e Parceiros';
    
    protected static ?string $modelLabel = 'Marca/Parceiro';
    
    protected static ?string $pluralModelLabel = 'Marcas e Parceiros';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informações do Parceiro')
                    ->description('Cadastre a logo e o nome das empresas parceiras que aparecerão no site.')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nome da Empresa')
                            ->placeholder('Ex: Gerdau, Holcim...')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\FileUpload::make('logo')
                            ->label('Logo')
                            ->helperText('Use preferencialmente arquivos SVG ou PNG com fundo transparente.')
                            ->image()
                            ->imageEditor()
                            ->directory('brands')
                            ->required(),
                        Forms\Components\Toggle::make('is_active')
                            ->label('Exibir no Site')
                            ->default(true)
                            ->required(),
                        Forms\Components\TextInput::make('sort_order')
                            ->label('Ordem de Exibição')
                            ->helperText('Números menores aparecem primeiro.')
                            ->numeric()
                            ->default(0)
                            ->required(),
                    ])->columns(2)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->striped()
            ->columns([
                Tables\Columns\ImageColumn::make('logo')
                    ->label('Logo'),
                Tables\Columns\TextColumn::make('name')
                    ->label('Nome')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Ativo')
                    ->boolean()
                    ->sortable(),
                Tables\Columns\TextColumn::make('sort_order')
                    ->label('Ordem')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Criado em')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
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
            ])
            ->defaultSort('sort_order');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBrands::route('/'),
            'create' => Pages\CreateBrand::route('/create'),
            'edit' => Pages\EditBrand::route('/{record}/edit'),
        ];
    }
}
