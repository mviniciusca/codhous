<?php

namespace App\Filament\Resources;

use App\Enums\AccessArea;
use App\Filament\Clusters\ToolsCluster;
use App\Filament\Concerns\BelongsToArea;
use App\Filament\Resources\VolumeCalculationResource\Pages;
use App\Models\VolumeCalculation;
use App\Models\VolumeFormula;
use Filament\Pages\SubNavigationPosition;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Histórico de cálculos de volume. Somente leitura: cada registro guarda as
 * entradas, a memória de cálculo e a versão exata da fórmula usada.
 */
class VolumeCalculationResource extends Resource
{
    use BelongsToArea;

    public static function getAccessArea(): AccessArea
    {
        return AccessArea::Comercial;
    }

    protected static ?string $model = VolumeCalculation::class;

    protected static ?string $cluster = ToolsCluster::class;

    /** Submenu do cluster em abas no topo: a calculadora precisa da largura toda */
    protected static SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;

    protected static bool $hasTitleCaseModelLabel = false;

    protected static ?string $slug = 'historico-de-calculos';

    protected static ?string $navigationIcon = 'heroicon-o-clock';

    protected static ?int $navigationSort = 3;

    protected static ?string $modelLabel = 'cálculo de volume';

    protected static ?string $pluralModelLabel = 'Histórico de cálculos';

    protected static ?string $navigationLabel = 'Histórico de cálculos';

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit(Model $record): bool
    {
        return false;
    }

    public static function canDelete(Model $record): bool
    {
        return false;
    }

    public static function canDeleteAny(): bool
    {
        return false;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->with(['formulaVersion.formula', 'user']))
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Data')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
                Tables\Columns\TextColumn::make('reference')
                    ->label('Identificação')
                    ->placeholder('Sem identificação')
                    ->searchable()
                    ->wrap(),
                Tables\Columns\TextColumn::make('formulaVersion.formula.name')
                    ->label('Fórmula')
                    ->description(fn (VolumeCalculation $record) => $record->formulaVersion?->label()),
                Tables\Columns\TextColumn::make('base_volume')
                    ->label('Volume base')
                    ->formatStateUsing(fn ($state) => number_format((float) $state, 3, ',', '.').' m³')
                    ->alignEnd(),
                Tables\Columns\TextColumn::make('margin_percent')
                    ->label('Margem')
                    ->formatStateUsing(fn ($state) => rtrim(rtrim(number_format((float) $state, 2, ',', '.'), '0'), ',').'%')
                    ->alignEnd(),
                Tables\Columns\TextColumn::make('final_volume')
                    ->label('Volume final')
                    ->formatStateUsing(fn ($state) => number_format((float) $state, 2, ',', '.').' m³')
                    ->weight('semibold')
                    ->alignEnd()
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Por')
                    ->placeholder('—'),
            ])
            ->filters([
                Filter::make('mine')
                    ->label('Somente os meus')
                    ->toggle()
                    ->query(fn (Builder $query) => $query->where('user_id', auth()->id())),
                SelectFilter::make('formula')
                    ->label('Fórmula')
                    ->options(fn () => VolumeFormula::ordered()->pluck('name', 'id'))
                    ->query(fn (Builder $query, array $data) => $query->when(
                        $data['value'] ?? null,
                        fn (Builder $q, $id) => $q->whereHas('formulaVersion', fn (Builder $v) => $v->where('volume_formula_id', $id)),
                    )),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('Memória')
                    ->icon('heroicon-m-document-text')
                    ->modalHeading(fn (VolumeCalculation $record) => 'Memória de cálculo · '.number_format($record->final_volume, 2, ',', '.').' m³')
                    ->modalWidth('lg')
                    ->infolist([])
                    ->modalContent(fn (VolumeCalculation $record) => view('filament.volume-calculations.memory', ['calculation' => $record])),
            ])
            ->recordAction('view')
            ->emptyStateIcon('heroicon-o-cube')
            ->emptyStateHeading('Nenhum cálculo salvo')
            ->emptyStateDescription('Use “Salvar no histórico” na calculadora de volume para guardar um cálculo aqui.');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListVolumeCalculations::route('/'),
        ];
    }
}
