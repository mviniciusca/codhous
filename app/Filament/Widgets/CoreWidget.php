<?php

namespace App\Filament\Widgets;

use App\Models\Module;
use Filament\Tables\Table;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Widgets\TableWidget as BaseWidget;

class CoreWidget extends BaseWidget
{
    // protected int|string|array $columnSpan = 'full';
    protected static ?int $sort = 7;
    public function table(Table $table): Table
    {
        return $table
            ->heading(__('Modules'))
            ->query(Module::query()
                ->select()
                ->take(5))
            ->columns([
                ToggleColumn::make('module.header')
                    ->alignCenter()
                    ->label(__('Header')),
                ToggleColumn::make('module.contact')
                    ->alignCenter()
                    ->label(__('Contact')),
                ToggleColumn::make('module.budget')
                    ->alignCenter()
                    ->label(__('Budget Tool')),
                ToggleColumn::make('module.newsletter')
                    ->alignCenter()
                    ->label(__('Newsletter')),
                ToggleColumn::make('module.footer')
                    ->alignCenter()
                    ->label(__('Footer')),
            ])
            ->paginated(false);
    }
}
