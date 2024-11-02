<?php

namespace App\Livewire\Budget;

use App\Models\BudgetHistory;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Livewire\Component;

class HistoryTable extends Component implements HasForms, HasTable
{
    use InteractsWithTable;
    use InteractsWithForms;

    public $data;

    public function mount($data)
    {
        $this->data = $data;
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(BudgetHistory::query()
                ->where('budget_id', '=', $this->data->first()->budget_id)
                ->with(['user', 'budget'])
                ->take(10)
                ->orderByDesc('created_at')
            )
            ->columns([
                TextColumn::make('user.name'),
                TextColumn::make('budget.code'),
                TextColumn::make('action')->badge(),
                TextColumn::make('updated_at'),
            ]);
    }

    public function render()
    {
        return view('livewire.budget.history-table');
    }
}
