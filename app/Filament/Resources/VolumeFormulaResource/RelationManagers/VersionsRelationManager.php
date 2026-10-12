<?php

namespace App\Filament\Resources\VolumeFormulaResource\RelationManagers;

use App\Filament\Resources\VolumeFormulaResource;
use App\Models\VolumeFormula;
use App\Models\VolumeFormulaVersion;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

/** Versões da fórmula: somente leitura, com a opção de restaurar uma versão antiga como nova */
class VersionsRelationManager extends RelationManager
{
    protected static string $relationship = 'versions';

    protected static ?string $title = 'Versões';

    protected static ?string $icon = 'heroicon-o-clock';

    public static function getBadge(Model $ownerRecord, string $pageClass): ?string
    {
        return (string) $ownerRecord->versions()->count();
    }

    public function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->with('author')->withCount('calculations'))
            ->defaultSort('version', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('version')
                    ->label('Versão')
                    ->formatStateUsing(fn ($state) => 'v'.$state)
                    ->badge()
                    ->color(fn (VolumeFormulaVersion $record) => $record->version === $this->getOwnerRecord()->currentVersion?->version ? 'primary' : 'gray'),
                Tables\Columns\TextColumn::make('definition.expression')
                    ->label('Expressão')
                    ->fontFamily('mono')
                    ->wrap(),
                Tables\Columns\TextColumn::make('definition.margin_percent')
                    ->label('Margem')
                    ->formatStateUsing(fn ($state) => number_format((float) $state, 2, ',', '.').'%'),
                Tables\Columns\TextColumn::make('notes')
                    ->label('O que mudou')
                    ->placeholder('—')
                    ->wrap(),
                Tables\Columns\TextColumn::make('calculations_count')
                    ->label('Cálculos')
                    ->numeric(),
                Tables\Columns\TextColumn::make('author.name')
                    ->label('Por')
                    ->placeholder('Sistema'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Em')
                    ->dateTime('d/m/Y H:i'),
            ])
            ->actions([
                Tables\Actions\Action::make('restore')
                    ->label('Restaurar')
                    ->icon('heroicon-m-arrow-uturn-left')
                    ->color('gray')
                    ->visible(fn (VolumeFormulaVersion $record) => $record->version !== $this->getOwnerRecord()->currentVersion?->version)
                    ->requiresConfirmation()
                    ->modalDescription(fn (VolumeFormulaVersion $record) => 'Cria uma nova versão com a regra da '.$record->label().'. Nada é apagado.')
                    ->action(function (VolumeFormulaVersion $record) {
                        /** @var VolumeFormula $formula */
                        $formula = $this->getOwnerRecord();
                        $version = $formula->saveVersion($record->toDefinition(), auth()->user(), 'Restaurada a partir da '.$record->label());

                        Notification::make()
                            ->title($version ? 'Versão restaurada como '.$version->label() : 'A regra dessa versão já é a atual')
                            ->success()
                            ->send();

                        $this->redirect(VolumeFormulaResource::getUrl('edit', ['record' => $formula]));
                    }),
            ])
            ->paginated([10, 25]);
    }
}
