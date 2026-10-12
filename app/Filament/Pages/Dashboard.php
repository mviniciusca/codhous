<?php

namespace App\Filament\Pages;

use App\Filament\Actions\QuickQuoteAction;
use App\Filament\Resources\BudgetResource;
use App\Services\DashboardOverview;
use Filament\Actions\Action;
use Filament\Pages\Dashboard as BaseDashboard;
use Illuminate\Contracts\Support\Htmlable;
use Livewire\Attributes\Computed;

/**
 * Visão geral: o que precisa de atenção hoje, a agenda de entregas e o andamento comercial.
 */
class Dashboard extends BaseDashboard
{
    protected static ?string $navigationLabel = 'Visão geral';

    protected static ?string $navigationIcon = 'heroicon-o-squares-2x2';

    protected static ?string $activeNavigationIcon = 'heroicon-s-squares-2x2';

    protected static string $view = 'filament.pages.dashboard';

    public function getTitle(): string|Htmlable
    {
        return 'Visão geral';
    }

    public function getHeading(): string|Htmlable
    {
        return $this->getGreeting().', '.$this->getUserName();
    }

    public function getSubheading(): string|Htmlable|null
    {
        $today = $this->agenda['today'];

        $summary = match ($today['count']) {
            0 => 'nenhuma entrega agendada para hoje',
            1 => '1 entrega agendada para hoje',
            default => $today['count'].' entregas agendadas para hoje',
        };

        if ($today['volume'] > 0) {
            $summary .= ' ('.$today['volumeLabel'].')';
        }

        return ucfirst($this->overview->now()->translatedFormat('l, j \d\e F')).' · '.$summary;
    }

    protected function getHeaderActions(): array
    {
        return [
            QuickQuoteAction::make(),
            Action::make('create')
                ->label('Novo orçamento')
                ->icon('heroicon-o-plus')
                ->url(BudgetResource::getUrl('create')),
        ];
    }

    /** A visão geral é montada na própria view, sem widgets */
    public function getWidgets(): array
    {
        return [];
    }

    #[Computed]
    public function overview(): DashboardOverview
    {
        return app(DashboardOverview::class);
    }

    #[Computed]
    public function agenda(): array
    {
        return $this->overview->agenda();
    }

    public function getGreeting(): string
    {
        $hour = $this->overview->now()->hour;

        return match (true) {
            $hour >= 5 && $hour < 12 => 'Bom dia',
            $hour >= 12 && $hour < 18 => 'Boa tarde',
            default => 'Boa noite',
        };
    }

    public function getUserName(): string
    {
        return explode(' ', auth()->user()?->name ?? 'Usuário')[0];
    }
}
