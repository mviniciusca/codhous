<?php

namespace App\Livewire;

use App\Models\BudgetHistory;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Livewire\Component;

class History extends Component implements HasForms, HasTable
{
    use InteractsWithTable;
    use InteractsWithForms;

    public function table(Table $table): Table
    {
        return $table
            ->query(BudgetHistory::query()
                ->select()
                ->with(['budget', 'user'])
                ->take(5)
            )

            ->columns([
                TextColumn::make('user.name'),
                TextColumn::make('action')
                    ->badge(),
                TextColumn::make('user.email'),
                TextColumn::make('budget.code'),
                TextColumn::make('updated_at'),
            ])
            ->filters([
                // ...
            ])
            ->actions([
                // ...
            ])
            ->bulkActions([
                // ...
            ]);
    }

    public function render()
    {
        return view('livewire.history');
    }
}
