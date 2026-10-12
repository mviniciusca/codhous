<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PosterDesignResource\Pages;
use App\Models\PosterDesign;
use App\Services\PosterStudio\Formats;
use App\Services\PosterStudio\Templates;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class PosterDesignResource extends Resource
{
    protected static ?string $model = PosterDesign::class;

    protected static ?string $slug = 'poster-builder';

    protected static ?string $navigationIcon = 'heroicon-o-photo';

    protected static ?int $navigationSort = 3;

    protected static ?string $modelLabel = 'arte';

    protected static ?string $pluralModelLabel = 'artes';

    protected static ?string $navigationLabel = 'Estúdio de Posts';

    protected static ?string $recordTitleAttribute = 'name';

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->with('user'))
            ->defaultSort('updated_at', 'desc')
            ->contentGrid(['md' => 2, 'lg' => 3, 'xl' => 4, '2xl' => 5])
            ->columns([
                Tables\Columns\Layout\Stack::make([
                    Tables\Columns\ViewColumn::make('thumbnail')
                        ->view('filament.resources.poster-design-resource.thumbnail-column'),
                    Tables\Columns\TextColumn::make('name')
                        ->label(__('Nome'))
                        ->weight('semibold')
                        ->searchable()
                        ->sortable(),
                    Tables\Columns\Layout\Split::make([
                        Tables\Columns\TextColumn::make('format')
                            ->formatStateUsing(fn (PosterDesign $record) => Formats::label($record->format).' · '.$record->width.'×'.$record->height)
                            ->color('gray')
                            ->size('xs'),
                        Tables\Columns\TextColumn::make('variant_label')
                            ->badge()
                            ->color('primary')
                            ->grow(false),
                    ]),
                    Tables\Columns\TextColumn::make('updated_at')
                        ->label(__('Editado'))
                        ->since()
                        ->sortable()
                        ->color('gray')
                        ->size('xs')
                        ->prefix(__('Editado ')),
                ])->space(2),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('format')
                    ->label(__('Formato'))
                    ->options(collect(Formats::all())->map(fn ($format) => $format['label'])->put(Formats::CUSTOM, 'Personalizado')->all()),
                Tables\Filters\SelectFilter::make('template_id')
                    ->label(__('Template'))
                    ->options(collect(Templates::catalog())->pluck('name', 'id')->all()),
            ])
            ->recordUrl(fn (PosterDesign $record) => static::getUrl('studio', ['record' => $record]))
            ->actions([
                Tables\Actions\Action::make('open')
                    ->label(__('Editar'))
                    ->icon('heroicon-m-pencil-square')
                    ->url(fn (PosterDesign $record) => static::getUrl('studio', ['record' => $record])),
                Tables\Actions\Action::make('duplicate')
                    ->label(__('Duplicar'))
                    ->icon('heroicon-m-document-duplicate')
                    ->color('gray')
                    ->action(function (PosterDesign $record, $livewire) {
                        $copy = Pages\PosterStudio::duplicateRecord($record);
                        $livewire->redirect(static::getUrl('studio', ['record' => $copy]));
                    }),
                Tables\Actions\DeleteAction::make()
                    ->modalDescription(__('A arte e suas miniaturas serão excluídas. Variações criadas a partir dela continuam existindo.')),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ])
            ->emptyStateHeading(__('Nenhuma arte ainda'))
            ->emptyStateDescription(__('Escolha um template acima para criar a primeira.'))
            ->emptyStateIcon('heroicon-o-photo');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPosterDesigns::route('/'),
            'studio' => Pages\PosterStudio::route('/{record}'),
        ];
    }
}
