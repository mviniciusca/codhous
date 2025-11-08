<?php

namespace App\Filament\Resources\UserResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ActivitiesRelationManager extends RelationManager
{
    protected static string $relationship = 'activities';

    protected static ?string $title = 'Activity Log';

    public function form(Form $form): Form
    {
        return $form->schema([]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('description')
            ->columns([
                Tables\Columns\TextColumn::make('log_name')
                    ->label('Log')
                    ->badge()
                    ->color('info'),

                Tables\Columns\TextColumn::make('description')
                    ->label('Action')
                    ->searchable(),

                Tables\Columns\TextColumn::make('subject_type')
                    ->label('Subject')
                    ->formatStateUsing(fn ($state) => class_basename($state))
                    ->badge(),

                Tables\Columns\TextColumn::make('event')
                    ->badge()
                    ->colors([
                        'success' => 'created',
                        'warning' => 'updated',
                        'danger'  => 'deleted',
                        'info'    => fn ($state) => ! in_array($state, ['created', 'updated', 'deleted']),
                    ]),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Date')
                    ->dateTime('d/m/Y H:i:s')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('event')
                    ->options([
                        'created' => 'Created',
                        'updated' => 'Updated',
                        'deleted' => 'Deleted',
                    ]),
                Tables\Filters\SelectFilter::make('subject_type')
                    ->label('Subject Type')
                    ->options([
                        'App\\Models\\Budget'   => 'Budget',
                        'App\\Models\\Customer' => 'Customer',
                        'App\\Models\\Mail'     => 'Mail',
                    ]),
            ])
            ->headerActions([
                // No create action
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->modalHeading('Activity Details')
                    ->modalContent(fn ($record) => view('filament.resources.user-resource.activity-details', ['activity' => $record])),
            ])
            ->bulkActions([
                // No bulk actions
            ])
            ->emptyStateHeading('No activity yet')
            ->emptyStateDescription('This user has not performed any tracked actions yet.')
            ->emptyStateIcon('heroicon-o-clock');
    }
}
