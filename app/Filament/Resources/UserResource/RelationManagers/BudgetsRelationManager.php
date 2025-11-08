<?php

namespace App\Filament\Resources\UserResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class BudgetsRelationManager extends RelationManager
{
    protected static string $relationship = 'budgets';

    protected static ?string $title = 'Budgets History';

    public function form(Form $form): Form
    {
        return $form->schema([]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('code')
            ->columns([
                Tables\Columns\TextColumn::make('code')
                    ->label('Code')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('content.customer_name')
                    ->label('Customer')
                    ->searchable()
                    ->limit(30),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'done',
                        'danger'  => 'cancelled',
                        'info'    => fn ($state) => ! in_array($state, ['pending', 'done', 'cancelled']),
                    ])
                    ->formatStateUsing(fn ($state) => ucfirst($state)),

                Tables\Columns\TextColumn::make('content.total')
                    ->label('Total Value')
                    ->money('BRL')
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending'   => 'Pending',
                        'done'      => 'Done',
                        'cancelled' => 'Cancelled',
                    ]),
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Active'),
            ])
            ->headerActions([
                // Removed create action - budgets should be created from Budget resource
            ])
            ->actions([
                Tables\Actions\Action::make('view')
                    ->label('View')
                    ->icon('heroicon-o-eye')
                    ->url(fn ($record): string => route('filament.admin.resources.budgets.edit', ['record' => $record->id]))
                    ->openUrlInNewTab(),
            ])
            ->bulkActions([
                // No bulk actions needed
            ])
            ->emptyStateHeading('No budgets yet')
            ->emptyStateDescription('This user has not created any budgets yet.')
            ->emptyStateIcon('heroicon-o-currency-dollar');
    }
}
