<?php

namespace App\Filament\Resources\PageResource\Concerns;

use App\Http\Controllers\PageBuilderPreviewController;
use Filament\Forms\Components\Builder;
use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\Renderless;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

/**
 * Live preview (estilo WordPress) para o Page Builder.
 *
 * Gera um snapshot do estado ATUAL do formulário (inclusive alterações ainda
 * não salvas e o que está sendo digitado no painel de edição do bloco),
 * guarda em cache e devolve a URL interna que renderiza a página completa.
 */
trait InteractsWithLivePreview
{
    #[Renderless]
    public function generateLivePreviewUrl(): ?string
    {
        $builder = $this->getLivePreviewBuilder();

        if (! $builder) {
            return null;
        }

        $blocks = $this->resolveLivePreviewBlocks($builder);

        $labels = [];
        foreach ($blocks as $uuid => $block) {
            $labels[$uuid] = (string) ($builder->getBlock($block['type'] ?? '')?->getLabel() ?? ($block['type'] ?? 'Bloco'));
        }

        $raw = $this->data ?? [];
        $token = static::makeLivePreviewToken($this->getLivePreviewScope());

        Cache::put(PageBuilderPreviewController::CACHE_PREFIX . $token, [
            'user_id' => (string) auth()->id(),
            'title' => (string) (data_get($raw, 'title') ?: 'Pré-visualização'),
            'slug' => (string) (data_get($raw, 'slug') ?: 'preview'),
            'meta' => [
                'title' => $this->normalizeLivePreviewValue(data_get($raw, 'meta.title')),
                'description' => $this->normalizeLivePreviewValue(data_get($raw, 'meta.description')),
                'keywords' => $this->normalizeLivePreviewValue(data_get($raw, 'meta.keywords')),
            ],
            'blocks' => $blocks,
            'labels' => $labels,
        ], now()->addMinutes(PageBuilderPreviewController::TTL_MINUTES));

        return route('page-builder.preview', ['token' => $token]);
    }

    public static function makeLivePreviewToken(string $scope): string
    {
        return hash_hmac('sha256', session()->getId() . '|' . auth()->id() . '|' . $scope, (string) config('app.key'));
    }

    protected function getLivePreviewScope(): string
    {
        $record = method_exists($this, 'getRecord') ? $this->getRecord() : null;

        return static::class . ':' . ($record?->getKey() ?? 'new');
    }

    protected function getLivePreviewBuilder(): ?Builder
    {
        $component = $this->form->getComponent(
            fn ($component): bool => $component instanceof Builder && $component->getName() === 'content',
            withHidden: true,
        );

        return $component instanceof Builder ? $component : null;
    }

    /**
     * @return array<string, array{type: string, data: array<string, mixed>}>
     */
    protected function resolveLivePreviewBlocks(Builder $builder): array
    {
        // Dehidrata o estado sem rodar validação nem salvar uploads temporários.
        // Pulamos o `mutateDehydratedState` de propósito para manter as chaves (UUID)
        // de cada bloco, usadas para sincronizar preview <-> lista de blocos.
        $state = [];
        $builder->dehydrateState($state);
        $blocks = data_get($state, $builder->getStatePath(), []);

        if (! is_array($blocks)) {
            $blocks = [];
        }

        // Sobrepõe com os dados do bloco que está sendo editado no painel lateral.
        if ($override = $this->getLivePreviewEditingOverride($builder)) {
            [$uuid, $data] = $override;

            if (isset($blocks[$uuid])) {
                $blocks[$uuid]['data'] = $data;
            }
        }

        $normalized = [];
        foreach ($blocks as $uuid => $block) {
            if (! is_array($block) || blank($block['type'] ?? null)) {
                continue;
            }

            $normalized[(string) $uuid] = [
                'type' => (string) $block['type'],
                'data' => (array) $this->normalizeLivePreviewValue($block['data'] ?? []),
            ];
        }

        return $normalized;
    }

    /**
     * @return array{0: string, 1: array<string, mixed>}|null
     */
    protected function getLivePreviewEditingOverride(Builder $builder): ?array
    {
        $builderKey = $builder->getKey();

        foreach ($this->mountedFormComponentActions ?? [] as $index => $actionName) {
            if ($actionName !== $builder->getEditActionName()) {
                continue;
            }

            if (($this->mountedFormComponentActionsComponents[$index] ?? null) !== $builderKey) {
                continue;
            }

            $uuid = $this->mountedFormComponentActionsArguments[$index]['item'] ?? null;

            if (blank($uuid)) {
                return null;
            }

            try {
                $form = $this->getMountedFormComponentActionForm($index);

                if (! $form) {
                    return null;
                }

                $state = [];
                $form->dehydrateState($state);

                $data = data_get($state, "mountedFormComponentActionsData.{$index}", []);
            } catch (\Throwable $e) {
                report($e);

                return null;
            }

            return [(string) $uuid, is_array($data) ? $data : []];
        }

        return null;
    }

    protected function normalizeLivePreviewValue(mixed $value): mixed
    {
        if ($value instanceof TemporaryUploadedFile) {
            try {
                return $value->temporaryUrl();
            } catch (\Throwable) {
                return null;
            }
        }

        if (is_array($value)) {
            return array_map(fn ($item) => $this->normalizeLivePreviewValue($item), $value);
        }

        if ($value instanceof \BackedEnum) {
            return $value->value;
        }

        if ($value instanceof \DateTimeInterface) {
            return $value->format('Y-m-d H:i:s');
        }

        if ($value instanceof \Illuminate\Contracts\Support\Arrayable) {
            return $this->normalizeLivePreviewValue($value->toArray());
        }

        if ($value instanceof \Stringable) {
            return (string) $value;
        }

        if (is_object($value)) {
            return null;
        }

        return $value;
    }
}
