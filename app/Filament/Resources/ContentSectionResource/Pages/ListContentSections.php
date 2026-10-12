<?php

namespace App\Filament\Resources\ContentSectionResource\Pages;

use App\Filament\Resources\ContentSectionResource;
use App\Models\ContentSection;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;

/**
 * Central das seções globais: cards com o skeleton de cada tipo, filtros, uso nas páginas
 * e ordenação por arrastar e soltar (grava sort_order).
 */
class ListContentSections extends Page
{
    protected static string $resource = ContentSectionResource::class;

    protected static string $view = 'filament.content-sections.index';

    public const STATUSES = ['todas' => 'Todas', 'ativas' => 'Ativas', 'inativas' => 'Inativas'];

    public const USAGES = ['home' => 'Na homepage', 'paginas' => 'Em uso nas páginas', 'sem-uso' => 'Não utilizadas'];

    #[Url(as: 'busca', except: '')]
    public string $search = '';

    #[Url(as: 'tipo', except: '')]
    public string $type = '';

    #[Url(except: 'todas')]
    public string $status = 'todas';

    #[Url(as: 'uso', except: '')]
    public string $usage = '';

    #[Url(as: 'visao', except: 'grade')]
    public string $display = 'grade';

    /** @var array<int, int|string> */
    public array $selected = [];

    /** A página é um Livewire próprio: confere a área do resource no mount e em cada requisição */
    public static function canAccess(array $parameters = []): bool
    {
        return static::getResource()::canViewAny();
    }

    public function getTitle(): string|Htmlable
    {
        return 'Seções do site';
    }

    /** Listagem é o nível raiz do resource: sem trilha antes do título */
    public function getBreadcrumbs(): array
    {
        return [];
    }

    public function getSubheading(): ?string
    {
        return 'Módulos globais reutilizados nas páginas pelo bloco "Seção pronta". Arraste os cards para mudar a ordem.';
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('create')
                ->label('Nova seção')
                ->icon('heroicon-o-plus')
                ->url(ContentSectionResource::getUrl('create'))
                ->visible(ContentSectionResource::canCreate()),
        ];
    }

    // ---------------------------------------------------------------- Dados

    /** Todas as seções na ordem salva (base dos indicadores, da posição e da reordenação) */
    #[Computed]
    public function allSections(): Collection
    {
        return ContentSection::query()->ordered()->get(['id', 'slug', 'name', 'type', 'is_active', 'sort_order', 'updated_at']);
    }

    #[Computed]
    public function usageMap(): array
    {
        return ContentSection::usageMap();
    }

    #[Computed]
    public function sections(): Collection
    {
        $usage = $this->usageMap;
        $types = ContentSection::typeLabels();
        $query = Str::lower(Str::ascii(trim($this->search)));
        $type = array_key_exists($this->type, $types) ? $this->type : null;

        return $this->allSections->filter(function (ContentSection $section) use ($usage, $types, $query, $type): bool {
            if ($type && $section->type !== $type) {
                return false;
            }

            if ($this->status === 'ativas' && ! $section->is_active || $this->status === 'inativas' && $section->is_active) {
                return false;
            }

            $usages = $usage[$section->id] ?? [];
            $matchesUsage = match ($this->usage) {
                'home' => collect($usages)->contains('is_home', true),
                'paginas' => $usages !== [],
                'sem-uso' => $usages === [],
                default => true,
            };

            if (! $matchesUsage) {
                return false;
            }

            if ($query === '') {
                return true;
            }

            $haystack = Str::lower(Str::ascii(implode(' ', [$section->name, $section->slug, $types[$section->type] ?? $section->type])));

            return collect(explode(' ', $query))->filter()->every(fn (string $word): bool => str_contains($haystack, $word));
        })->values();
    }

    public function hasFilters(): bool
    {
        return $this->search !== '' || $this->type !== '' || $this->status !== 'todas' || $this->usage !== '';
    }

    protected function getViewData(): array
    {
        $all = $this->allSections;
        $usage = $this->usageMap;

        return [
            'sections' => $this->sections,
            'positions' => $all->pluck('id')->flip()->map(fn (int $index): int => $index + 1)->all(),
            // Não use a chave "usage": colide com a propriedade do filtro
            'usageMap' => $usage,
            'catalog' => ContentSection::typeCatalog(),
            'stats' => [
                'total' => $all->count(),
                'active' => $all->where('is_active', true)->count(),
                'used' => $all->filter(fn (ContentSection $section): bool => isset($usage[$section->id]))->count(),
                'home' => $all->filter(fn (ContentSection $section): bool => collect($usage[$section->id] ?? [])->contains('is_home', true))->count(),
            ],
            'statusCounts' => [
                'todas' => $all->count(),
                'ativas' => $all->where('is_active', true)->count(),
                'inativas' => $all->where('is_active', false)->count(),
            ],
            'canReorder' => ContentSectionResource::canReorder() && ! $this->hasFilters() && $all->count() > 1,
            'canEdit' => ContentSectionResource::canEdit(...),
            'canCreate' => ContentSectionResource::canCreate(),
            'canDeleteAny' => ContentSectionResource::canDeleteAny(),
        ];
    }

    // ---------------------------------------------------------------- Filtros

    public function updated(string $property, mixed $value = null): void
    {
        // Valores fora da lista (URL editada à mão) voltam ao padrão
        match ($property) {
            'type' => $this->type = array_key_exists($this->type, ContentSection::typeLabels()) ? $this->type : '',
            'status' => $this->status = array_key_exists($this->status, self::STATUSES) ? $this->status : 'todas',
            'usage' => $this->usage = array_key_exists($this->usage, self::USAGES) ? $this->usage : '',
            'display' => $this->display = in_array($this->display, ['grade', 'lista'], true) ? $this->display : 'grade',
            default => null,
        };
    }

    public function mount(): void
    {
        foreach (['type', 'status', 'usage', 'display'] as $property) {
            $this->updated($property);
        }
    }

    /** Descarta os dados calculados para a próxima renderização refletir a alteração */
    protected function refreshSections(): void
    {
        unset($this->allSections, $this->sections, $this->usageMap);
    }

    public function clearFilters(): void
    {
        $this->reset('search', 'type', 'status', 'usage');
    }

    // ---------------------------------------------------------------- Ordenação

    /**
     * Grava a nova ordem. Só vale para a lista completa (sem filtros), para não embaralhar seções ocultas.
     *
     * @param  array<int, int|string>  $ids
     */
    public function reorderSections(array $ids): void
    {
        abort_unless(ContentSectionResource::canReorder(), 403);

        $ids = array_values(array_unique(array_map('intval', $ids)));
        $current = $this->allSections->pluck('id')->all();

        if ($this->hasFilters() || count($ids) !== count($current) || array_diff($current, $ids) !== []) {
            $this->refreshSections();

            Notification::make()
                ->title('A ordem não foi salva')
                ->body('A lista mudou ou está filtrada. Limpe os filtros e tente de novo.')
                ->warning()
                ->send();

            return;
        }

        if ($ids === $current) {
            return;
        }

        DB::transaction(function () use ($ids) {
            foreach ($ids as $index => $id) {
                ContentSection::whereKey($id)->update(['sort_order' => $index + 1]);
            }
        });

        $this->refreshSections();

        Notification::make()->title('Ordem atualizada')->success()->send();
    }

    /** Alternativa ao arrastar (teclado e menu): move uma posição para cima (-1) ou para baixo (1) */
    public function moveSection(int|string $id, int $direction): void
    {
        $ids = $this->allSections->pluck('id')->all();
        $from = array_search((int) $id, $ids, true);
        $to = $from === false ? false : $from + ($direction < 0 ? -1 : 1);

        if ($to === false || ! isset($ids[$to])) {
            return;
        }

        [$ids[$from], $ids[$to]] = [$ids[$to], $ids[$from]];

        $this->reorderSections($ids);
    }

    // ---------------------------------------------------------------- Ações por seção

    /** Seção alvo de uma ação montada com { record: id } */
    protected function sectionFromArguments(array $arguments): ?ContentSection
    {
        return ContentSection::find((int) ($arguments['record'] ?? 0));
    }

    public function toggleActiveAction(): Action
    {
        $usages = fn (?ContentSection $record): array => $record ? ($this->usageMap[$record->id] ?? []) : [];

        return Action::make('toggleActive')
            ->record(fn (array $arguments) => $this->sectionFromArguments($arguments))
            ->authorize(fn (?ContentSection $record): bool => $record && ContentSectionResource::canEdit($record))
            // Pede confirmação só ao desativar uma seção que está em uso
            ->requiresConfirmation(fn (?ContentSection $record): bool => $record?->is_active && $usages($record) !== [])
            ->modalIcon('heroicon-o-eye-slash')
            ->modalHeading(fn (?ContentSection $record): string => 'Desativar "'.$record?->name.'"?')
            ->modalDescription(function (?ContentSection $record) use ($usages): string {
                $pages = collect($usages($record))->pluck('title')->unique()->implode(', ');

                return "A seção deixa de aparecer em: {$pages}. Os blocos continuam nas páginas e voltam a aparecer quando você reativar.";
            })
            ->modalSubmitActionLabel('Desativar')
            ->color('warning')
            ->action(function (ContentSection $record) {
                $record->is_active = ! $record->is_active;
                $record->save();

                $this->refreshSections();

                Notification::make()
                    ->title($record->is_active ? 'Seção ativada' : 'Seção desativada')
                    ->body($record->name)
                    ->success()
                    ->send();
            });
    }

    public function duplicateAction(): Action
    {
        return Action::make('duplicate')
            ->record(fn (array $arguments) => $this->sectionFromArguments($arguments))
            ->authorize(fn (?ContentSection $record): bool => $record && ContentSectionResource::canCreate())
            ->action(function (ContentSection $record) {
                $slug = Str::limit($record->slug, 200, '').'-copia';
                $base = $slug;

                for ($i = 2; ContentSection::where('slug', $slug)->exists(); $i++) {
                    $slug = "{$base}-{$i}";
                }

                // A cópia nasce inativa e no fim da lista, para não mudar o site antes da revisão
                $copy = $record->replicate();
                $copy->name = $record->name.' (cópia)';
                $copy->slug = $slug;
                $copy->is_active = false;
                $copy->sort_order = (int) ContentSection::max('sort_order') + 1;
                $copy->save();

                $this->refreshSections();

                Notification::make()
                    ->title('Seção duplicada')
                    ->body("\"{$copy->name}\" foi criada inativa.")
                    ->success()
                    ->actions([
                        \Filament\Notifications\Actions\Action::make('edit')
                            ->label('Editar cópia')
                            ->url(ContentSectionResource::getUrl('edit', ['record' => $copy])),
                    ])
                    ->send();
            });
    }

    public function deleteAction(): DeleteAction
    {
        return DeleteAction::make()
            ->record(fn (array $arguments) => $this->sectionFromArguments($arguments))
            // Substitui o padrão do DeleteAction, que exige um registro
            ->hidden(fn (?ContentSection $record): bool => $record === null)
            ->authorize(fn (?ContentSection $record): bool => $record && ContentSectionResource::canDelete($record))
            ->modalHeading(fn (?ContentSection $record): string => 'Excluir "'.$record?->name.'"?')
            ->modalDescription(function (?ContentSection $record): string {
                $pages = collect($record ? ($this->usageMap[$record->id] ?? []) : [])->pluck('title')->unique();

                return $pages->isEmpty()
                    ? 'A seção não é usada em nenhuma página. Esta ação não pode ser desfeita.'
                    : 'Ela é usada em: '.$pages->implode(', ').'. Esses blocos deixarão de exibir conteúdo. Esta ação não pode ser desfeita.';
            })
            ->after(function () {
                $this->selected = [];
                $this->refreshSections();
            });
    }

    // ---------------------------------------------------------------- Ações em massa

    /** Seleção atual, limitada às seções que ainda existem */
    protected function selectedSections(): Collection
    {
        return ContentSection::whereKey(array_map('intval', $this->selected))->get();
    }

    protected function bulkStatusAction(string $name, bool $active): Action
    {
        return Action::make($name)
            ->authorize(fn (): bool => $this->selected !== [])
            ->action(function () use ($active) {
                $sections = $this->selectedSections()->filter(fn (ContentSection $section): bool => ContentSectionResource::canEdit($section));

                $sections->each(fn (ContentSection $section) => $section->forceFill(['is_active' => $active])->save());

                $this->selected = [];
                $this->refreshSections();

                Notification::make()
                    ->title(trans_choice($active ? ':count seção ativada|:count seções ativadas' : ':count seção desativada|:count seções desativadas', $sections->count()))
                    ->success()
                    ->send();
            });
    }

    public function bulkActivateAction(): Action
    {
        return $this->bulkStatusAction('bulkActivate', true);
    }

    public function bulkDeactivateAction(): Action
    {
        return $this->bulkStatusAction('bulkDeactivate', false);
    }

    public function bulkDeleteAction(): Action
    {
        return Action::make('bulkDelete')
            ->authorize(fn (): bool => $this->selected !== [] && ContentSectionResource::canDeleteAny())
            ->requiresConfirmation()
            ->color('danger')
            ->modalIcon('heroicon-o-trash')
            ->modalHeading(fn (): string => trans_choice('Excluir :count seção?|Excluir :count seções?', count($this->selected)))
            ->modalDescription(function (): string {
                $used = $this->selectedSections()->filter(fn (ContentSection $section): bool => isset($this->usageMap[$section->id]));

                return $used->isEmpty()
                    ? 'Esta ação não pode ser desfeita.'
                    : 'Em uso nas páginas: '.$used->pluck('name')->implode(', ').'. Esses blocos deixarão de exibir conteúdo. Esta ação não pode ser desfeita.';
            })
            ->modalSubmitActionLabel('Excluir')
            ->action(function () {
                $sections = $this->selectedSections()->filter(fn (ContentSection $section): bool => ContentSectionResource::canDelete($section));

                $sections->each->delete();

                $this->selected = [];
                $this->refreshSections();

                Notification::make()
                    ->title(trans_choice(':count seção excluída|:count seções excluídas', $sections->count()))
                    ->success()
                    ->send();
            });
    }
}
