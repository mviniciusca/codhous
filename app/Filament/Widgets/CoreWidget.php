<?php

namespace App\Filament\Widgets;

use App\Models\Module;
use Filament\Tables\Table;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Widgets\TableWidget as BaseWidget;

class CoreWidget extends BaseWidget
{
    public function table(Table $table): Table
    {
        return $table
            ->query(Module::query()
                ->select()
                ->take(5))
            ->columns([
                ToggleColumn::make('module.footer')
                    ->label(__('Footer'))
            ]);
    }
}
