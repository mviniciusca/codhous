<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers;
use App\Filament\Resources\ProductResource\RelationManagers\ProductOptionRelationManager;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationGroup = 'Orçamentos';
    protected static ?int $navigationSort = 2;
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $breadcrumb = 'Produtos';

    public static function getNavigationLabel(): string
    {
        return 'Produtos';
    }

    public static function getModelLabel(): string
    {
        return 'Produto';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Produtos';
    }

    public static function count(): ?string
    {
        $count = Product::query()
            ->where('is_active', true)
            ->count();

        return $count !== 0 ? $count : null;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->columns(6)
            ->schema([
                Group::make()
                    ->columnSpan(4)
                    ->schema([
                        Section::make('Produto')
                            ->description('Crie ou edite as informações principais do seu produto.')
                            ->icon('heroicon-o-shopping-bag')
                            ->columns(2)
                            ->schema([
                                TextInput::make('name')
                                    ->required()
                                    ->maxLength(255)
                                    ->label('Nome do Produto')
                                    ->placeholder('Ex: Concreto Usinado')
                                    ->helperText('O nome que aparecerá para o cliente no site.'),
                            ]),
                    ]),
                Group::make()
                    ->columnSpan(2)
                    ->schema([
                        Section::make('Status')
                            ->description('Controle de visibilidade.')
                            ->icon('heroicon-o-cog-6-tooth')
                            ->columns(2)
                            ->schema([
                                Toggle::make('is_active')
                                    ->label('Ativo')
                                    ->default(true)
                                    ->helperText('Se desativado, o produto não aparecerá no site.')
                                    ->inline(),
                            ]),
                    ]),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->striped()
            ->searchable()
            ->searchPlaceholder('Pesquisar produtos...')
            ->columns([
                TextColumn::make('name')
                    ->label('Produto')
                    ->searchable(),
                IconColumn::make('is_active')
                    ->boolean()
                    ->alignCenter()
                    ->label('Status'),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                ActionGroup::make([
                    Tables\Actions\EditAction::make()
                        ->label('Editar'),
                    Tables\Actions\DeleteAction::make()
                        ->label('Excluir'),
                    Tables\Actions\RestoreAction::make()
                        ->label('Restaurar'),
                    Tables\Actions\ForceDeleteAction::make()
                        ->label('Excluir Permanentemente'),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('Excluir Selecionados'),
                    Tables\Actions\RestoreBulkAction::make()
                        ->label('Restaurar Selecionados'),
                    Tables\Actions\ForceDeleteBulkAction::make()
                        ->label('Excluir Permanentemente Selecionados'),
                ]),
            ]);
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public static function getRelations(): array
    {
        return [
            ProductOptionRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit'   => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
