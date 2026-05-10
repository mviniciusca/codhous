<?php

namespace App\Filament\Resources;

use App\Filament\Clusters\MediaCluster;
use App\Filament\Resources\SocialPostResource\Pages;
use App\Models\SocialPost;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Pages\SubNavigationPosition;

class SocialPostResource extends Resource
{
    public static function getSubNavigationPosition(): SubNavigationPosition
    {
        return SubNavigationPosition::Top;
    }

    protected static ?string $model = SocialPost::class;

    protected static ?string $cluster = MediaCluster::class;

    protected static ?string $navigationIcon = 'heroicon-o-sparkles';

    protected static ?int $navigationSort = 1;

    public static function getNavigationLabel(): string
    {
        return 'Artes Geradas';
    }

    public static function getModelLabel(): string
    {
        return 'Arte Gerada';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Artes Geradas';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Detalhes da Arte')
                    ->schema([
                        Forms\Components\TextInput::make('text')
                            ->label('Texto')
                            ->disabled(),
                        Forms\Components\TextInput::make('status')
                            ->label('Status')
                            ->disabled(),
                        Forms\Components\DateTimePicker::make('generated_at')
                            ->label('Gerada em')
                            ->disabled(),

                        Forms\Components\SpatieMediaLibraryFileUpload::make('output')
                            ->label('Arte Final')
                            ->collection('output')
                            ->image()
                            ->disabled()
                            ->columnSpanFull(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('output_url')
                    ->label('Arte')
                    ->square()
                    ->size(80),

                Tables\Columns\TextColumn::make('text')
                    ->label('Texto')
                    ->limit(50)
                    ->searchable(),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'done' => 'success',
                        'pending' => 'warning',
                        'failed' => 'danger',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('generated_at')
                    ->label('Data')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'done' => 'Concluídas',
                        'pending' => 'Pendentes',
                        'failed' => 'Falhas',
                    ]),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make()->label('Ver'),
                    Tables\Actions\Action::make('download')
                        ->label('Baixar')
                        ->icon('heroicon-o-arrow-down-tray')
                        ->color('info')
                        ->url(fn (SocialPost $record) => $record->output_url)
                        ->openUrlInNewTab()
                        ->visible(fn (SocialPost $record) => $record->isGenerated()),
                    Tables\Actions\DeleteAction::make()->label('Excluir'),
                ]),
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
            'index' => Pages\ListSocialPosts::route('/'),
        ];
    }
    
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->latest();
    }
}
