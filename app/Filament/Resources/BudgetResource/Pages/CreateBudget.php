<?php

namespace App\Filament\Resources\BudgetResource\Pages;

use Filament\Actions;
use Filament\Forms\Form;
use App\Filament\Resources\BudgetResource;
use Filament\Resources\Pages\CreateRecord;

class CreateBudget extends CreateRecord
{
    protected static string $resource = BudgetResource::class;

    public function form(Form $form): Form
    {
        return $form
            ->schema([

            ]);
    }
}
