<?php

namespace App\Filament\Widgets;

use App\Models\Module;
use App\Models\Setting;
use Filament\Tables\Table;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Widgets\TableWidget as BaseWidget;

class CoreWidget extends BaseWidget
{
    // protected int|string|array $columnSpan = 'full';
    protected static ?int $sort = 7;
    public function table(Table $table): Table
    {
        return $table
            ->heading(__('Quick Access'))
            ->headerActions([
                Action::make('settings')
                    ->icon('heroicon-o-cog-6-tooth')
                    ->url(route('filament.admin.resources.settings.edit', Setting::first()->id))
            ])
            ->description(__('Manage your app modules here.'))
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
