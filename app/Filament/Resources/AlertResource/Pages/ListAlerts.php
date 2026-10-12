<?php

namespace App\Filament\Resources\AlertResource\Pages;

use App\Filament\Resources\AlertResource;
use App\Models\Alert;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;
use Filament\Support\Enums\MaxWidth;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;

/**
 * Central de alertas: indicadores, filtros e cards com uma miniatura de cada alerta.
 * Ações rápidas: ativar/desativar, pré-visualizar no site, duplicar, reordenar e excluir.
 */
class ListAlerts extends Page
{
    protected static string $resource = AlertResource::class;

    protected static string $view = 'filament.alerts.index';

    public const STATUSES = [
        Alert::STATUS_ACTIVE => 'Ativos',
        Alert::STATUS_SCHEDULED => 'Agendados',
        Alert::STATUS_EXPIRED => 'Encerrados',
        Alert::STATUS_DRAFT => 'Rascunhos',
    ];

    public const SORTS = ['ordem' => 'Ordem de exibição', 'recentes' => 'Mais recentes', 'nome' => 'Nome (A–Z)'];

    #[Url(as: 'busca', except: '')]
    public string $search = '';

    #[Url(as: 'tipo', except: '')]
    public string $type = '';

    #[Url(as: 'estilo', except: '')]
    public string $style = '';

    #[Url(except: '')]
    public string $status = '';

    #[Url(as: 'ordem', except: 'ordem')]
    public string $sort = 'ordem';

    #[Url(as: 'visao', except: 'grade')]
    public string $display = 'grade';

    /** A página é um Livewire próprio: confere a área do resource no mount e em cada requisição */
    public static function canAccess(array $parameters = []): bool
    {
        return static::getResource()::canViewAny();
    }

    public function getTitle(): string|Htmlable
    {
        return 'Alertas e notificações';
    }

    /** Listagem é o nível raiz do resource: sem trilha antes do título */
    public function getBreadcrumbs(): array
    {
        return [];
    }

    public function getSubheading(): ?string
    {
        return 'Avisos exibidos no site: promoções, comunicados e mensagens de sistema. Ative, desative e pré-visualize direto nos cards.';
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('create')
                ->label('Novo alerta')
                ->icon('heroicon-o-plus')
                ->url(AlertResource::getUrl('create'))
                ->visible(AlertResource::canCreate()),
        ];
    }

    // ---------------------------------------------------------------- Dados

    /** Todos os alertas na ordem de exibição do site (base dos indicadores e da reordenação) */
    #[Computed]
    public function allAlerts(): Collection
    {
        return Alert::query()->orderBy('sort_order')->orderBy('id')->get();
    }

    #[Computed]
    public function alerts(): Collection
    {
        $query = Str::lower(Str::ascii(trim($this->search)));
        $types = Alert::typeLabels();
        $styles = Alert::styleLabels();

        $alerts = $this->allAlerts->filter(function (Alert $alert) use ($query, $types, $styles): bool {
            if ($this->type !== '' && $alert->type !== $this->type) {
                return false;
            }

            if ($this->style !== '' && $alert->style !== $this->style) {
                return false;
            }

            if ($this->status !== '' && $alert->status() !== $this->status) {
                return false;
            }

            if ($query === '') {
                return true;
            }

            $haystack = Str::lower(Str::ascii(implode(' ', [
                $alert->name, $alert->title, $alert->message, $types[$alert->type] ?? '', $styles[$alert->style] ?? '',
            ])));

            return collect(explode(' ', $query))->filter()->every(fn (string $word): bool => str_contains($haystack, $word));
        });

        $sorted = match ($this->sort) {
            'recentes' => $alerts->sortByDesc(fn (Alert $alert) => $alert->updated_at?->getTimestamp() ?? 0),
            'nome' => $alerts->sortBy(fn (Alert $alert) => Str::lower(Str::ascii($alert->name))),
            default => $alerts,
        };

        return $sorted->values();
    }

    public function hasFilters(): bool
    {
        return $this->search !== '' || $this->type !== '' || $this->style !== '' || $this->status !== '';
    }

    /** Mover para cima/baixo só faz sentido na ordem de exibição e com a lista completa */
    public function canReorder(): bool
    {
        return AlertResource::canEdit(new Alert) && $this->sort === 'ordem' && ! $this->hasFilters() && $this->allAlerts->count() > 1;
    }

    protected function getViewData(): array
    {
        $all = $this->allAlerts;
        $statuses = $all->map->status();

        return [
            'alerts' => $this->alerts,
            'positions' => $all->pluck('id')->flip()->map(fn (int $index): int => $index + 1)->all(),
            'stats' => [
                'total' => $all->count(),
                Alert::STATUS_ACTIVE => $statuses->filter(fn (string $status): bool => $status === Alert::STATUS_ACTIVE)->count(),
                Alert::STATUS_DRAFT => $statuses->filter(fn (string $status): bool => $status === Alert::STATUS_DRAFT)->count(),
                Alert::STATUS_SCHEDULED => $statuses->filter(fn (string $status): bool => $status === Alert::STATUS_SCHEDULED)->count(),
                Alert::STATUS_EXPIRED => $statuses->filter(fn (string $status): bool => $status === Alert::STATUS_EXPIRED)->count(),
            ],
            'types' => Alert::typeCatalog(),
            'styles' => Alert::styleCatalog(),
            'statusCatalog' => Alert::statusCatalog(),
            'canReorder' => $this->canReorder(),
            'canCreate' => AlertResource::canCreate(),
        ];
    }

    public function getMaxContentWidth(): MaxWidth|string|null
    {
        return MaxWidth::Full;
    }

    // ---------------------------------------------------------------- Filtros

    public function updated(string $property, mixed $value = null): void
    {
        // Valores fora da lista (URL editada à mão) voltam ao padrão
        match ($property) {
            'type' => $this->type = array_key_exists($this->type, Alert::typeCatalog()) ? $this->type : '',
            'style' => $this->style = array_key_exists($this->style, Alert::styleCatalog()) ? $this->style : '',
            'status' => $this->status = array_key_exists($this->status, self::STATUSES) ? $this->status : '',
            'sort' => $this->sort = array_key_exists($this->sort, self::SORTS) ? $this->sort : 'ordem',
            'display' => $this->display = in_array($this->display, ['grade', 'lista'], true) ? $this->display : 'grade',
            default => null,
        };
    }

    public function mount(): void
    {
        foreach (['type', 'style', 'status', 'sort', 'display'] as $property) {
            $this->updated($property);
        }
    }

    public function clearFilters(): void
    {
        $this->reset('search', 'type', 'style', 'status');
    }

    /** Descarta os dados calculados para a próxima renderização refletir a alteração */
    protected function refreshAlerts(): void
    {
        unset($this->allAlerts, $this->alerts);
    }

    // ---------------------------------------------------------------- Ordenação

    /** Move uma posição para cima (-1) ou para baixo (1) e regrava a ordem de todos */
    public function moveAlert(int|string $id, int $direction): void
    {
        abort_unless($this->canReorder(), 403);

        $ids = $this->allAlerts->pluck('id')->all();
        $from = array_search((int) $id, $ids, true);
        $to = $from === false ? false : $from + ($direction < 0 ? -1 : 1);

        if ($to === false || ! isset($ids[$to])) {
            return;
        }

        [$ids[$from], $ids[$to]] = [$ids[$to], $ids[$from]];

        DB::transaction(function () use ($ids) {
            foreach ($ids as $index => $alertId) {
                Alert::whereKey($alertId)->update(['sort_order' => $index + 1]);
            }
        });

        $this->refreshAlerts();

        Notification::make()->title('Ordem atualizada')->success()->send();
    }

    // ---------------------------------------------------------------- Ações por alerta

    /** Alerta alvo de uma ação montada com { record: id } */
    protected function alertFromArguments(array $arguments): ?Alert
    {
        return Alert::find((int) ($arguments['record'] ?? 0));
    }

    public function toggleActiveAction(): Action
    {
        return Action::make('toggleActive')
            ->record(fn (array $arguments) => $this->alertFromArguments($arguments))
            ->authorize(fn (?Alert $record): bool => $record && AlertResource::canEdit($record))
            ->action(function (Alert $record) {
                $record->is_active = ! $record->is_active;
                $record->save();

                $this->refreshAlerts();

                $body = match ($record->status()) {
                    Alert::STATUS_ACTIVE => 'Já está aparecendo no site.',
                    Alert::STATUS_SCHEDULED => 'Aparece no site a partir de '.$record->start_at->format('d/m/Y \à\s H:i').'.',
                    Alert::STATUS_EXPIRED => 'Publicado, mas o período de exibição já terminou.',
                    default => 'Saiu do site e ficou como rascunho.',
                };

                Notification::make()
                    ->title($record->is_active ? 'Alerta publicado' : 'Alerta desativado')
                    ->body("\"{$record->name}\". {$body}")
                    ->status($record->status() === Alert::STATUS_EXPIRED ? 'warning' : 'success')
                    ->send();
            });
    }

    public function previewAction(): Action
    {
        return Action::make('preview')
            ->record(fn (array $arguments) => $this->alertFromArguments($arguments))
            ->authorize(fn (?Alert $record): bool => $record && AlertResource::canView($record))
            ->modalHeading(fn (?Alert $record): string => 'Pré-visualizar: '.$record?->name)
            ->modalDescription('O alerta sobre a página inicial do site, como o visitante vê.')
            ->modalWidth(MaxWidth::SevenExtraLarge)
            ->modalContent(fn (?Alert $record) => view('filament.alerts.preview-modal', ['alert' => $record]))
            ->modalSubmitAction(false)
            ->modalCancelActionLabel('Fechar')
            ->extraModalFooterActions(fn (?Alert $record): array => $record && AlertResource::canEdit($record) ? [
                Action::make('edit')
                    ->label('Editar alerta')
                    ->icon('heroicon-m-pencil-square')
                    ->url(AlertResource::getUrl('edit', ['record' => $record])),
            ] : []);
    }

    public function duplicateAction(): Action
    {
        return Action::make('duplicate')
            ->record(fn (array $arguments) => $this->alertFromArguments($arguments))
            ->authorize(fn (?Alert $record): bool => $record && AlertResource::canCreate())
            ->action(function (Alert $record) {
                $copy = static::duplicateAlert($record);

                $this->refreshAlerts();

                Notification::make()
                    ->title('Alerta duplicado')
                    ->body("\"{$copy->name}\" foi criado como rascunho.")
                    ->success()
                    ->actions([
                        \Filament\Notifications\Actions\Action::make('edit')
                            ->label('Editar cópia')
                            ->url(AlertResource::getUrl('edit', ['record' => $copy])),
                    ])
                    ->send();
            });
    }

    /** A cópia nasce como rascunho, no fim da lista e com cookie próprio */
    public static function duplicateAlert(Alert $alert): Alert
    {
        $copy = $alert->replicate();
        $copy->name = Str::limit($alert->name, 240, '').' (cópia)';
        $copy->is_active = false;
        $copy->cookie_key = null;
        $copy->sort_order = (int) Alert::max('sort_order') + 1;
        $copy->save();

        return $copy;
    }

    public function deleteAction(): DeleteAction
    {
        return DeleteAction::make()
            ->record(fn (array $arguments) => $this->alertFromArguments($arguments))
            // Substitui o padrão do DeleteAction, que exige um registro
            ->hidden(fn (?Alert $record): bool => $record === null)
            ->authorize(fn (?Alert $record): bool => $record && AlertResource::canDelete($record))
            ->modalHeading(fn (?Alert $record): string => 'Excluir "'.$record?->name.'"?')
            ->modalDescription(fn (?Alert $record): string => $record?->status() === Alert::STATUS_ACTIVE
                ? 'O alerta está aparecendo no site e sai imediatamente. Esta ação não pode ser desfeita.'
                : 'Esta ação não pode ser desfeita.')
            ->after(fn () => $this->refreshAlerts());
    }
}
