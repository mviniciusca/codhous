<?php

namespace App\Filament\Resources\BudgetResource\Pages;

use App\Models\Budget;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\ForceDeleteAction;
use Filament\Tables\Actions\RestoreAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\BudgetResource;
use Filament\Tables\Actions\ForceDeleteBulkAction;
use Filament\Tables\Actions\RestoreBulkAction;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class BudgetBin extends ListRecords
{
    protected static string $resource = BudgetResource::class;

    public static function count(): ?string
    {
        $count = Budget::onlyTrashed()->count();
        return $count !== 0 ? $count : null;
    }
    public function getTitle(): string|Htmlable
    {
        return __('Trash');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(Budget::onlyTrashed())
            ->columns([
                TextColumn::make('code')
                    ->searchable()
                    ->label(__('Code')),
                TextColumn::make('status')
                    ->sortable()
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'pending' => 'primary',
                        'on going' => 'warning',
                        'done' => 'success',
                        'ignored' => 'danger'
                    }),
                TextColumn::make('content.customer_name')
                    ->sortable()
                    ->searchable()
                    ->label(__('Name')),
                TextColumn::make('content.customer_email')
                    ->label(__('Email')),
                TextColumn::make('content.customer_phone')
                    ->label(__('Phone')),
                TextColumn::make('created_at')
                    ->date('d/m/Y H:i')
                    ->sortable()
                    ->label(__('Date')),
                IconColumn::make('is_active')
                    ->label(__('Active'))
                    ->alignCenter()
                    ->boolean()
            ])
            ->defaultSort('id', 'desc')
            ->filters([
                TernaryFilter::make('is_active')
                    ->placeholder(__('Default'))
                    ->label(__('Show Budgets'))
                    ->trueLabel(__('Active'))
                    ->falseLabel(__('Inactive')),
                SelectFilter::make('status')
                    ->placeholder(__('All Status'))
                    ->label(__('Status'))
                    ->options([
                        'pending' => __('Pending'),
                        'on going' => __('On Going'),
                        'done' => __('Done'),
                        'ignored' => __('Ignored'),
                    ])
                    ->searchable(),
            ])
            ->actions([
                ActionGroup::make([
                    DeleteAction::make(),
                    ForceDeleteAction::make(),
                    RestoreAction::make(),
                ])
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
