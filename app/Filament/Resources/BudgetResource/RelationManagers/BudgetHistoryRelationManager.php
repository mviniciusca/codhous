<?php

namespace App\Filament\Resources\BudgetResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Spatie\Activitylog\Models\Activity;

class BudgetHistoryRelationManager extends RelationManager
{
    protected static string $relationship = 'activities';

    public function table(Table $table): Table
    {
        return $table
            ->heading(__('Activity History'))
            ->description(__('Latest registers of activity in this document'))
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('causer.name')
                    ->label(__('Agent Name'))
                    ->default('System'),

                TextColumn::make('description')
                    ->label(__('Action')),

                TextColumn::make('created_at')
                    ->label(__('When'))
                    ->alignEnd()
                    ->dateTime('d/m/Y H:i'),

                TextColumn::make('properties')
                    ->label(__('Details'))
                    ->listWithLineBreaks()
                    ->visible(fn ($record): bool => ! empty($record->properties))
                    ->color('gray'),
            ]);
    }
}
