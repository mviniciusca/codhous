<?php

namespace App\Filament\Resources\PageResource\Concerns;

use Filament\Notifications\Notification;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Locked;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

/**
 * Rascunho, auto save e controle de alterações não salvas do editor de páginas.
 *
 * - O rascunho guarda o estado do formulário (conteúdo, SEO e configurações) em `pages.draft`
 *   sem tocar no que está publicado. "Publicar" é o save normal do Filament e limpa o rascunho.
 * - `hasPendingChanges` é recalculado no servidor a cada requisição comparando o hash do estado
 *   atual com o último estado persistido; o front usa isso para o status, o auto save e o
 *   aviso ao sair da página.
 *
 * Requer InteractsWithLivePreview (para incluir o bloco aberto no painel de edição).
 */
trait InteractsWithPageDrafts
{
    public ?string $draftSavedAt = null;

    public bool $hasPendingChanges = false;

    #[Locked]
    public ?string $persistedStateHash = null;

    abstract protected function persistDraft(array $payload): void;

    abstract protected function supportsAutosave(): bool;

    public function dehydrateInteractsWithPageDrafts(): void
    {
        $hash = $this->hashDraftPayload($this->getDraftPayload());

        $this->persistedStateHash ??= $hash;
        $this->hasPendingChanges = $hash !== $this->persistedStateHash;
    }

    public function saveDraft(bool $silent = false): void
    {
        $hasTemporaryUploads = $this->formHasTemporaryUploads();

        // Move uploads temporários para o storage, senão o rascunho perderia as imagens.
        $this->form->callBeforeStateDehydrated();

        $payload = $this->getDraftPayload();

        $this->persistDraft($payload);

        $this->persistedStateHash = $this->hashDraftPayload($payload);
        $this->hasPendingChanges = false;
        $this->draftSavedAt = now()->toIso8601String();

        if (! $silent) {
            Notification::make()
                ->success()
                ->title(__('Rascunho salvo'))
                ->body(__('As alterações ainda não estão no site. Clique em "Publicar" quando estiver pronto.'))
                ->send();
        }

        // O auto save não deve redesenhar o editor (perderia foco/scroll no painel aberto).
        if (! $hasTemporaryUploads) {
            $this->skipRender();
        }
    }

    public function getEditorConfig(): array
    {
        return [
            'autosave' => $this->supportsAutosave(),
            'autosaveDelay' => 3000,
        ];
    }

    protected function resetPersistedState(): void
    {
        $this->persistedStateHash = null;
        $this->hasPendingChanges = false;
    }

    /**
     * Estado do formulário pronto para ser salvo como rascunho, sem validação.
     */
    protected function getDraftPayload(): array
    {
        $state = [];
        $this->form->dehydrateState($state);

        $data = data_get($state, $this->form->getStatePath(), []);
        $data = is_array($data) ? $data : [];

        // Inclui o bloco aberto no painel lateral, mesmo antes de clicar em "Aplicar".
        if (($builder = $this->getLivePreviewBuilder()) && ($override = $this->getLivePreviewEditingOverride($builder))) {
            [$uuid, $blockData] = $override;

            if (isset($data['content'][$uuid])) {
                $data['content'][$uuid]['data'] = $blockData;
            }
        }

        return $this->normalizeDraftValue($data);
    }

    protected function hashDraftPayload(array $payload): string
    {
        return md5(json_encode($payload));
    }

    protected function normalizeDraftValue(mixed $value): mixed
    {
        return match (true) {
            is_array($value) => array_map(fn ($item) => $this->normalizeDraftValue($item), $value),
            $value instanceof \BackedEnum => $value->value,
            $value instanceof \DateTimeInterface => $value->format('Y-m-d H:i:s'),
            $value instanceof \Stringable => (string) $value,
            is_object($value) => null,
            default => $value,
        };
    }

    protected function formHasTemporaryUploads(): bool
    {
        $found = false;

        array_walk_recursive($this->data, function ($value) use (&$found) {
            $found = $found || $value instanceof TemporaryUploadedFile;
        });

        return $found;
    }

    /**
     * Ao falhar a validação ao publicar: mostra onde está o problema e troca para a aba certa.
     */
    protected function onValidationError(ValidationException $exception): void
    {
        $statePath = $this->form->getStatePath();
        $keys = array_keys($exception->errors());
        $contentKeys = array_filter($keys, fn (string $key) => Str::startsWith($key, "{$statePath}.content."));

        $this->dispatch('page-editor-tab', tab: count($contentKeys) === count($keys) ? 'content' : 'settings');

        $body = __('Revise os campos destacados.');

        if ($contentKeys) {
            $uuid = Str::of(Arr::first($contentKeys))->after("{$statePath}.content.")->before('.')->toString();
            $blocks = array_keys($this->data['content'] ?? []);
            $position = array_search($uuid, $blocks, true);
            $type = $this->data['content'][$uuid]['type'] ?? null;
            $label = $type ? (string) $this->getLivePreviewBuilder()?->getBlock($type)?->getLabel() : null;

            if ($position !== false) {
                $body = __('O bloco :n (:label) tem campos obrigatórios em branco. Abra-o na Estrutura para corrigir.', [
                    'n' => $position + 1,
                    'label' => $label ?: __('sem nome'),
                ]);
            }
        }

        Notification::make()
            ->danger()
            ->title(__('Não foi possível publicar'))
            ->body($body)
            ->send();
    }
}
