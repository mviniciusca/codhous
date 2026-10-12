<?php

namespace App\Filament\Resources\ContentSectionResource\Concerns;

use App\Filament\Concerns\NormalizesPreviewState;
use App\Http\Controllers\ContentSectionPreviewController;
use App\Models\ContentSection;
use Filament\Support\Enums\MaxWidth;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\Renderless;

/**
 * Construtor de seções (criação e edição): live preview da seção a partir do
 * estado ainda não salvo e as ações do rodapé, que ficam dentro do inspetor.
 */
trait InteractsWithSectionBuilder
{
    use NormalizesPreviewState;

    #[Renderless]
    public function generateSectionPreviewUrl(bool $chrome = false): ?string
    {
        $type = $this->data['type'] ?? $this->getSectionBuilderRecordType();

        if (blank($type) || ! array_key_exists($type, ContentSection::typeLabels())) {
            return null;
        }

        // Dehidrata sem validar nem salvar uploads: só os campos visíveis do tipo entram
        try {
            $state = [];
            $this->form->dehydrateState($state);
            $content = data_get($state, 'data.content', []);
        } catch (\Throwable $e) {
            report($e);
            $content = $this->data['content'] ?? [];
        }

        $token = hash_hmac(
            'sha256',
            session()->getId().'|'.auth()->id().'|'.static::class.':'.($this->getSectionBuilderRecordKey() ?? 'new'),
            (string) config('app.key'),
        );

        Cache::put(ContentSectionPreviewController::CACHE_PREFIX.$token, [
            'user_id' => (string) auth()->id(),
            'type' => $type,
            'content' => (array) $this->normalizePreviewValue(is_array($content) ? $content : []),
        ], now()->addMinutes(ContentSectionPreviewController::TTL_MINUTES));

        return route('content-sections.preview', array_filter(['token' => $token, 'chrome' => $chrome ? 1 : null]));
    }

    /**
     * Botões finais do inspetor: criar (e criar outra) ou salvar. Renderizados só
     * quando o wizard é exibido, pois o formulário é montado antes disso.
     */
    public function getSectionBuilderSubmitAction(): Htmlable
    {
        return $this->lazyHtml(function (): string {
            $actions = [$this->getSubmitFormAction()->label($this->getSectionBuilderSubmitLabel())];

            if (method_exists($this, 'getCreateAnotherFormAction') && static::canCreateAnother()) {
                $actions[] = $this->getCreateAnotherFormAction()->label('Criar e adicionar outra');
            }

            return collect($actions)->map->toHtml()->implode('');
        });
    }

    public function getSectionBuilderCancelAction(): Htmlable
    {
        return $this->lazyHtml(fn (): string => $this->getCancelFormAction()->toHtml());
    }

    protected function lazyHtml(\Closure $render): Htmlable
    {
        return new class($render) implements Htmlable
        {
            public function __construct(private \Closure $render) {}

            public function toHtml(): string
            {
                return ($this->render)();
            }
        };
    }

    // O rodapé com as ações fica no inspetor (wizard), não abaixo do formulário
    protected function getFormActions(): array
    {
        return [];
    }

    /**
     * Cabeçalho compacto (uma linha) para sobrar mais altura para o construtor.
     */
    public function getHeader(): ?View
    {
        return view('filament.section-builder.page-header', [
            'breadcrumbs' => $this->getBreadcrumbs(),
            'heading' => $this->getHeading(),
            'subheading' => $this->getSubheading(),
            'actions' => $this->getCachedHeaderActions(),
        ]);
    }

    public function getMaxContentWidth(): MaxWidth|string|null
    {
        return MaxWidth::Full;
    }

    protected function getSectionBuilderSubmitLabel(): string
    {
        return method_exists($this, 'getCreateAnotherFormAction') ? 'Criar seção' : 'Salvar alterações';
    }

    protected function getSectionBuilderRecordType(): ?string
    {
        return method_exists($this, 'getRecord') ? $this->getRecord()?->type : null;
    }

    protected function getSectionBuilderRecordKey(): mixed
    {
        return method_exists($this, 'getRecord') ? $this->getRecord()?->getKey() : null;
    }
}
