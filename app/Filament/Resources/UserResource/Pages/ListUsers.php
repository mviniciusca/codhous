<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use App\Models\User;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label(__('New Team Member'))
                ->icon('heroicon-o-user-plus'),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make(__('All Users'))
                ->badge(User::count()),

            'super_admins' => Tab::make(__('Super Admins'))
                ->modifyQueryUsing(fn (Builder $query) => $query->whereHas('roles', fn ($q) => $q->where('name', 'super_admin')))
                ->badge(User::whereHas('roles', fn ($q) => $q->where('name', 'super_admin'))->count())
                ->badgeColor('danger'),

            'admins' => Tab::make(__('Admins'))
                ->modifyQueryUsing(fn (Builder $query) => $query->whereHas('roles', fn ($q) => $q->where('name', 'admin')))
                ->badge(User::whereHas('roles', fn ($q) => $q->where('name', 'admin'))->count())
                ->badgeColor('warning'),

            'vendedores' => Tab::make(__('Sales Team'))
                ->modifyQueryUsing(fn (Builder $query) => $query->whereHas('roles', fn ($q) => $q->where('name', 'vendedor')))
                ->badge(User::whereHas('roles', fn ($q) => $q->where('name', 'vendedor'))->count())
                ->badgeColor('success'),

            'financeiro' => Tab::make(__('Financial'))
                ->modifyQueryUsing(fn (Builder $query) => $query->whereHas('roles', fn ($q) => $q->where('name', 'financeiro')))
                ->badge(User::whereHas('roles', fn ($q) => $q->where('name', 'financeiro'))->count())
                ->badgeColor('info'),

            'atendimento' => Tab::make(__('Customer Service'))
                ->modifyQueryUsing(fn (Builder $query) => $query->whereHas('roles', fn ($q) => $q->where('name', 'atendimento')))
                ->badge(User::whereHas('roles', fn ($q) => $q->where('name', 'atendimento'))->count())
                ->badgeColor('primary'),
        ];
    }
}
