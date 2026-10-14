<?php

namespace App\Filament\Resources\ContactAgendaResource\Pages;

use App\Filament\Resources\ContactAgendaResource;
use App\Filament\Resources\ContactAgendaResource\Widgets\ContactAgendaStatsOverview;
use App\Models\ContactAgenda;
use App\Models\ContactInteraction;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Computed;
use pxlrbt\FilamentExcel\Actions\Pages\ExportAction;

class ListContactAgenda extends ListRecords
{
    protected static string $resource = ContactAgendaResource::class;

    protected static string $view = 'filament.contact-agenda.list';

    public ?string $selectedDay = '2026-10-12';

    public string $month = '2026-10';

    public function getTitle(): string|Htmlable
    {
        return 'Agenda de Contatos';
    }

    public function getSubheading(): ?string
    {
        return 'Gerencie seus contatos, acompanhe interações e nunca perca um retorno.';
    }

    protected function getHeaderWidgets(): array
    {
        return [
            ContactAgendaStatsOverview::class,
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            ExportAction::make('export')
                ->label('Exportar')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('gray')
                ->exports([ContactAgendaResource::excelExport()->useTableQuery()]),

            Actions\CreateAction::make()
                ->label('Novo contato')
                ->icon('heroicon-o-plus'),
        ];
    }

    public function getTabs(): array
    {
        $count = fn (?string $category) => ContactAgenda::query()
            ->when($category, fn ($q) => $q->where('category', $category))
            ->count();

        return [
            'todos' => Tab::make('Todos')
                ->badge($count(null))
                ->badgeColor('gray'),

            'clientes' => Tab::make('Clientes')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('category', ContactAgenda::CATEGORY_CLIENT))
                ->badge($count(ContactAgenda::CATEGORY_CLIENT))
                ->badgeColor('success')
                ->icon('heroicon-m-user'),

            'leads' => Tab::make('Leads')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('category', ContactAgenda::CATEGORY_LEAD))
                ->badge($count(ContactAgenda::CATEGORY_LEAD))
                ->badgeColor('info')
                ->icon('heroicon-m-bolt'),

            'fornecedores' => Tab::make('Fornecedores')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('category', ContactAgenda::CATEGORY_SUPPLIER))
                ->badge($count(ContactAgenda::CATEGORY_SUPPLIER))
                ->badgeColor('purple')
                ->icon('heroicon-m-truck'),

            'parceiros' => Tab::make('Parceiros')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('category', ContactAgenda::CATEGORY_PARTNER))
                ->badge($count(ContactAgenda::CATEGORY_PARTNER))
                ->badgeColor('warning')
                ->icon('heroicon-m-user-group'),
        ];
    }

    public function getDefaultActiveTab(): string|int|null
    {
        return 'todos';
    }

    public function selectCategoryTab(string $tabName): void
    {
        $this->activeTab = $tabName;
    }

    public function previousMonth(): void
    {
        $date = CarbonImmutable::createFromFormat('!Y-m', $this->month)->subMonth();
        $this->month = $date->format('Y-m');
    }

    public function nextMonth(): void
    {
        $date = CarbonImmutable::createFromFormat('!Y-m', $this->month)->addMonth();
        $this->month = $date->format('Y-m');
    }

    public function goToToday(): void
    {
        $now = Carbon::parse('2026-10-12');
        $this->month = $now->format('Y-m');
        $this->selectedDay = $now->format('Y-m-d');
    }

    public function selectDay(?string $day): void
    {
        if ($this->selectedDay === $day) {
            $this->selectedDay = null;
        } else {
            $this->selectedDay = $day;
        }
    }

    #[Computed]
    public function categoryCounts(): array
    {
        return [
            'client' => ContactAgenda::where('category', ContactAgenda::CATEGORY_CLIENT)->count(),
            'lead' => ContactAgenda::where('category', ContactAgenda::CATEGORY_LEAD)->count(),
            'supplier' => ContactAgenda::where('category', ContactAgenda::CATEGORY_SUPPLIER)->count(),
            'partner' => ContactAgenda::where('category', ContactAgenda::CATEGORY_PARTNER)->count(),
        ];
    }

    #[Computed]
    public function calendar(): array
    {
        $monthDate = CarbonImmutable::createFromFormat('!Y-m', $this->month)->locale('pt_BR');
        $label = ucfirst($monthDate->translatedFormat('F \\d\\e Y'));

        $startOfMonth = $monthDate->startOfMonth();
        $endOfMonth = $monthDate->endOfMonth();

        $startDayOfWeek = $startOfMonth->dayOfWeek; // 0 = Dom, 6 = Sáb
        $calendarStart = $startOfMonth->subDays($startDayOfWeek);

        $endDayOfWeek = $endOfMonth->dayOfWeek;
        $calendarEnd = $endOfMonth->addDays(6 - $endDayOfWeek);

        $interactions = ContactInteraction::query()
            ->whereBetween('scheduled_at', [$calendarStart->startOfDay(), $calendarEnd->endOfDay()])
            ->get();

        $eventsByDate = [];
        foreach ($interactions as $interaction) {
            $d = $interaction->scheduled_at->format('Y-m-d');
            $eventsByDate[$d][] = $interaction->type;
        }

        $weeks = [];
        $current = $calendarStart;

        while ($current->lte($calendarEnd)) {
            $week = [];
            for ($i = 0; $i < 7; $i++) {
                $dateStr = $current->format('Y-m-d');
                $types = array_values(array_unique($eventsByDate[$dateStr] ?? []));

                $week[] = [
                    'day' => $current->day,
                    'fullDate' => $dateStr,
                    'inMonth' => $current->month === $monthDate->month,
                    'isToday' => $current->isSameDay(Carbon::parse('2026-10-12')),
                    'isSelected' => $this->selectedDay === $dateStr,
                    'types' => $types,
                ];
                $current = $current->addDay();
            }
            $weeks[] = $week;
        }

        return [
            'label' => $label,
            'weeks' => $weeks,
        ];
    }

    #[Computed]
    public function upcomingInteractions()
    {
        return ContactInteraction::with('contact')
            ->where('status', ContactInteraction::STATUS_PENDING)
            ->orderBy('scheduled_at', 'asc')
            ->take(4)
            ->get();
    }
}
