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

class BudgetHistoryRelationManager extends RelationManager
{
    protected static string $relationship = 'budgetHistory';

    public function table(Table $table): Table
    {
        return $table
            ->heading(__('Activity History'))
            ->description(__('Latest registers of activity in this document'))
            ->defaultSort('created_at', 'desc')
            ->recordTitleAttribute('id')
            ->columns([
                TextColumn::make('user.name')
                    ->label(__('Agent Name')),
                TextColumn::make('action')
                    ->label(__('Action'))
                    ->badge()
                    ->color(function ($state) {
                        return match ($state) {
                            'update' => 'primary',
                            'create' => 'success',
                            'delete' => 'danger',
                            default  => 'primary',
                        };
                    }),
                TextColumn::make('created_at')
                    ->label(__('When'))
                    ->alignEnd()
                    ->date('d/m/Y H:i'),

            ]);
    }
}
