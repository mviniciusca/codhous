<?php

namespace App\Filament\Resources\NewsletterResource\Pages;

use Filament\Actions;
use App\Models\Newsletter;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\ForceDeleteBulkAction;
use Filament\Tables\Actions\RestoreBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Columns\ToggleColumn;
use App\Filament\Resources\NewsletterResource;
use Filament\Tables\Actions\DeleteBulkAction;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SubscriberBin extends ListRecords
{
    protected static string $resource = NewsletterResource::class;
    public function getTitle(): string|Htmlable
    {
        return __('Trash');
    }
    public static function count(): ?string
    {
        $count = Newsletter::onlyTrashed()->count();
        return $count !== 0 ? $count : null;
    }
    public function table(\Filament\Tables\Table $table): \Filament\Tables\Table
    {
        return $table
            ->searchable()
            ->query(Newsletter::onlyTrashed())
            ->columns([
                ToggleColumn::make('is_active')
                    ->label(__('Status')),
                TextColumn::make('name')
                    ->label(__('Name')),
                TextColumn::make('email')
                    ->label(__('Email')),
            ])
            ->filters([])
            ->bulkActions([
                BulkActionGroup::make([
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ])
            ]);
    }
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
