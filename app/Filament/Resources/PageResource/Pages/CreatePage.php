<?php

namespace App\Filament\Resources\PageResource\Pages;

use App\Filament\Resources\PageResource;
use App\Filament\Resources\PageResource\Concerns\InteractsWithLivePreview;
use App\Filament\Resources\PageResource\Concerns\InteractsWithPageDrafts;
use App\Models\Page;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Str;

class CreatePage extends CreateRecord
{
    use InteractsWithLivePreview;
    use InteractsWithPageDrafts;

    protected static string $resource = PageResource::class;

    protected static string $view = 'filament.page-builder.editor';

    public function getHeading(): string
    {
        return '';
    }

    public function getBreadcrumbs(): array
    {
        return [];
    }

    protected function getFormActions(): array
    {
        return [];
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('saveDraft')
                ->label('Salvar rascunho')
                ->icon('heroicon-m-document-arrow-down')
                ->color('gray')
                ->action(fn () => $this->saveDraft()),
            Actions\Action::make('create')
                ->label('Publicar')
                ->icon('heroicon-m-globe-alt')
                ->color('primary')
                ->action('create')
                ->keyBindings(['mod+shift+s']),
        ];
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        return array_merge($data, ['published_at' => now()]);
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Página publicada';
    }

    /**
     * Uma página nova salva como rascunho vira um registro oculto (fora do menu) que
     * guarda tudo em `draft`; o editor continua dali, já com auto save.
     */
    protected function persistDraft(array $payload): void
    {
        $title = trim((string) ($payload['title'] ?? '')) ?: 'Página sem título';
        $slug = Str::slug((string) ($payload['slug'] ?? '')) ?: Str::slug($title);

        if (Page::withTrashed()->where('slug', $slug)->exists()) {
            $slug .= '-'.Str::lower(Str::random(4));
        }

        $payload = array_merge($payload, ['title' => $title, 'slug' => $slug]);

        $page = Page::create([
            'title' => $title,
            'slug' => $slug,
            'content' => [],
            'meta' => [],
            'is_visible' => false,
            'is_active_in_menu' => false,
            'sort_order' => (int) ($payload['sort_order'] ?? 0),
            'draft' => $payload,
            'draft_saved_at' => now(),
        ]);

        Notification::make()
            ->success()
            ->title('Rascunho criado')
            ->body('A página ainda não está no site. O editor agora salva automaticamente.')
            ->send();

        $this->redirect(PageResource::getUrl('edit', ['record' => $page]));
    }

    public function saveDraft(bool $silent = false): void
    {
        // Na criação o rascunho sempre gera um registro e redireciona: nunca é silencioso.
        $this->form->callBeforeStateDehydrated();
        $this->persistDraft($this->getDraftPayload());
    }

    protected function supportsAutosave(): bool
    {
        return false;
    }
}
