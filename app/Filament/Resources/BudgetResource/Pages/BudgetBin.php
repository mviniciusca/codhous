<?php

namespace App\Filament\Resources\BudgetResource\Pages;

use App\Filament\Resources\BudgetResource;
use App\Models\Budget;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Columns\TextColumn;

class BudgetBin extends ListRecords
{
    protected static string $resource = BudgetResource::class;

    public static function count(): ?string
    {
        $count = Budget::onlyTrashed()->count();
        return $count !== 0 ? $count : null;
    }

    public function table(\Filament\Tables\Table $table): \Filament\Tables\Table
    {
        return $table
            ->query(Budget::onlyTrashed())
            ->columns([
                TextColumn::make('code')
            ]);
    }
}
