<?php

namespace App\Filament\Widgets;

use App\Models\Module;
use Filament\Tables\Table;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Widgets\TableWidget as BaseWidget;

class CoreWidget extends BaseWidget
{
    // protected int|string|array $columnSpan = 'full';
    protected static ?int $sort = 0;
    public function table(Table $table): Table
    {
        return $table
            ->heading(__('Core Features'))
            ->query(Module::query()
                ->select()
                ->take(5))
            ->columns([
                ToggleColumn::make('module.header')
                    ->label(__('Header')),
                ToggleColumn::make('module.contact')
                    ->label(__('Contact')),
                ToggleColumn::make('module.budget')
                    ->label(__('Budget Tool')),
                ToggleColumn::make('module.newsletter')
                    ->label(__('Newsletter')),
                ToggleColumn::make('module.footer')
                    ->label(__('Footer')),
            ])
            ->paginated(false);
    }
}
