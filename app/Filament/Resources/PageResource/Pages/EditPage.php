<?php

namespace App\Filament\Resources\PageResource\Pages;

use App\Filament\Resources\PageResource;
use App\Filament\Resources\PageResource\Concerns\InteractsWithLivePreview;
use App\Filament\Resources\PageResource\Concerns\InteractsWithPageDrafts;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Pboivin\FilamentPeek\Pages\Actions\PreviewAction;
use Pboivin\FilamentPeek\Pages\Concerns\HasPreviewModal;

class EditPage extends EditRecord
{
    use HasPreviewModal;
    use InteractsWithLivePreview;
    use InteractsWithPageDrafts;

    protected static string $resource = PageResource::class;

    protected static string $view = 'filament.page-builder.editor';

    public function mount(int|string $record): void
    {
        parent::mount($record);

        $this->draftSavedAt = $this->getRecord()->draft_saved_at?->toIso8601String();
    }

    protected function getPreviewModalUrl(): ?string
    {
        return $this->getRecord()->getPublicUrl();
    }

    public function getTitle(): string
    {
        return 'Editar Página: '.$this->getRecord()->title;
    }

    // O cabeçalho padrão é substituído pelo cabeçalho compacto do editor.
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
            Actions\Action::make('save')
                ->label('Publicar')
                ->icon('heroicon-m-globe-alt')
                ->color('primary')
                ->action('save')
                ->keyBindings(['mod+shift+s']),
            Actions\ActionGroup::make([
                Actions\Action::make('visit')
                    ->label('Ver página publicada')
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->url(fn () => $this->getRecord()->getPublicUrl())
                    ->openUrlInNewTab()
                    ->visible(fn () => $this->getRecord()->is_visible),
                PreviewAction::make()
                    ->label('Preview interno')
                    ->icon('heroicon-o-eye'),
                Actions\Action::make('discardDraft')
                    ->label('Descartar rascunho')
                    ->icon('heroicon-o-arrow-uturn-left')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->modalHeading('Descartar rascunho?')
                    ->modalDescription('As alterações não publicadas serão perdidas e o editor volta para a versão que está no site.')
                    ->modalSubmitActionLabel('Descartar')
                    ->visible(fn () => filled($this->draftSavedAt))
                    ->action(fn () => $this->discardDraft()),
                Actions\DeleteAction::make()
                    ->label('Excluir página')
                    ->icon('heroicon-o-trash'),
            ])
                ->icon('heroicon-m-ellipsis-vertical')
                ->color('gray')
                ->button()
                ->hiddenLabel()
                ->tooltip('Mais opções'),
        ];
    }

    /**
     * Ao abrir o editor, carrega o rascunho (se houver) por cima da versão publicada.
     */
    protected function mutateFormDataBeforeFill(array $data): array
    {
        $draft = $this->getRecord()->draft;

        return is_array($draft) && $draft ? array_replace($data, $draft) : $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        return array_merge($data, [
            'draft' => null,
            'draft_saved_at' => null,
            'published_at' => now(),
        ]);
    }

    protected function afterSave(): void
    {
        $this->draftSavedAt = null;
        $this->resetPersistedState();
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return 'Página publicada';
    }

    public function discardDraft(): void
    {
        $this->getRecord()->forceFill(['draft' => null, 'draft_saved_at' => null])->saveQuietly();

        $this->draftSavedAt = null;
        $this->fillForm();
        $this->resetPersistedState();

        Notification::make()->success()->title('Rascunho descartado')->send();
    }

    protected function persistDraft(array $payload): void
    {
        $this->getRecord()->forceFill([
            'draft' => $payload,
            'draft_saved_at' => now(),
        ])->saveQuietly();
    }

    protected function supportsAutosave(): bool
    {
        return true;
    }
}
