<?php

namespace App\Filament\Resources;

use App\Filament\Resources\NewsletterResource\Pages;
use App\Filament\Resources\NewsletterResource\Widgets\NewsletterOverwview;
use App\Models\Newsletter;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class NewsletterResource extends Resource
{
    protected static ?string $model = Newsletter::class;

    protected static ?string $slug = 'subscribers';

    protected static ?string $navigationIcon = 'heroicon-o-envelope';

    protected static ?string $navigationGroup = 'Website';
    protected static ?int $navigationSort = 2;

    public static function getNavigationBadge(): ?string
    {
        if (static::getModel()::count() != 0) {
            return static::getModel()::count();
        }

        return null;
    }

    public static function getNavigationLabel(): string
    {
        return 'Inscritos na Newsletter';
    }

    public static function getModelLabel(): string
    {
        return 'Inscrito';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Inscritos na Newsletter';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->columns(6)
            ->schema([
                Toggle::make('is_active')
                    ->label('Status')
                    ->helperText('Define se o inscrito está ativo para receber e-mails.')
                    ->columnSpan(1)
                    ->inline(false),
                TextInput::make('name')
                    ->label('Nome')
                    ->helperText('Nome fornecido pelo usuário.')
                    ->columnSpan(3)
                    ->disabled(),
                TextInput::make('email')
                    ->label('E-mail')
                    ->helperText('Endereço de e-mail do inscrito.')
                    ->columnSpan(2)
                    ->disabled(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                IconColumn::make('is_active')
                    ->label('Ativo')
                    ->boolean()
                    ->alignCenter(),
                TextColumn::make('name')
                    ->label('Nome')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('email')
                    ->label('E-mail')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Inscrito em')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                TernaryFilter::make('is_active')
                    ->label('Status')
                    ->placeholder('Todos')
                    ->trueLabel('Ativos')
                    ->falseLabel('Inativos')
                    ->default(true),
            ])
            ->actions([
                ActionGroup::make([
                    Tables\Actions\DeleteAction::make()
                        ->label('Remover Inscrito'),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('Remover Selecionados'),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getWidgets(): array
    {
        return [
            NewsletterOverwview::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListNewsletters::route('/'),
            'create' => Pages\CreateNewsletter::route('/create'),
            'edit'   => Pages\EditNewsletter::route('/{record}/edit'),
            'bin'    => Pages\SubscriberBin::route('/bin'),
        ];
    }
}
