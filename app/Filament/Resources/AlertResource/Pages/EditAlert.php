<?php

namespace App\Filament\Resources\AlertResource\Pages;

use App\Filament\Resources\AlertResource;
use App\Filament\Resources\AlertResource\Concerns\InteractsWithAlertEditor;
use App\Models\Alert;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditAlert extends EditRecord
{
    use InteractsWithAlertEditor;

    protected static string $resource = AlertResource::class;

    /** Status pedido pela ação: publicar (true), despublicar (false) ou manter (null, Enter e Ctrl+S) */
    protected ?bool $targetActive = null;

    /** Título da notificação quando a ação muda o status */
    protected ?string $savedTitle = null;

    public function getTitle(): string
    {
        return 'Editar alerta';
    }

    public function getSubheading(): ?string
    {
        return 'As alterações aparecem na prévia na hora, mas só chegam ao site depois de salvar.';
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('duplicate')
                ->label('Duplicar')
                ->icon('heroicon-m-document-duplicate')
                ->color('gray')
                ->visible(AlertResource::canCreate())
                ->action(function () {
                    $copy = ListAlerts::duplicateAlert($this->getRecord());

                    Notification::make()->title('Alerta duplicado')->body("\"{$copy->name}\" foi criado como rascunho.")->success()->send();

                    $this->redirect(AlertResource::getUrl('edit', ['record' => $copy]));
                }),
            Actions\DeleteAction::make(),
        ];
    }

    public function publish(): void
    {
        $this->targetActive = true;
        $this->savedTitle = 'Alerta publicado';

        $this->save();
    }

    public function unpublish(): void
    {
        $this->targetActive = false;
        $this->savedTitle = 'Alerta despublicado';

        $this->save();
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if ($this->targetActive !== null) {
            $data['is_active'] = $this->targetActive;
        }

        return $data;
    }

    protected function afterSave(): void
    {
        // As ações da barra dependem do status, que pode ter mudado neste salvamento.
        // cacheFormActions() acrescenta à lista em vez de substituir: zera antes para não duplicar a barra
        $this->cachedFormActions = [];
        $this->cacheFormActions();
    }

    protected function getFormActions(): array
    {
        /** @var Alert $alert */
        $alert = $this->getRecord();

        $cancel = $this->getCancelFormAction()->extraAttributes(['class' => 'al-cancel']);

        if ($alert->is_active) {
            return [
                $cancel,
                Actions\Action::make('unpublish')
                    ->label('Despublicar')
                    ->icon('heroicon-m-eye-slash')
                    ->color('gray')
                    ->requiresConfirmation()
                    ->modalIcon('heroicon-o-eye-slash')
                    ->modalHeading('Despublicar o alerta?')
                    ->modalDescription('As alterações são salvas e o alerta sai do site, ficando como rascunho.')
                    ->modalSubmitActionLabel('Despublicar')
                    ->action(fn () => $this->unpublish()),
                $this->getSaveFormAction()
                    ->label('Salvar alterações')
                    ->icon('heroicon-m-check'),
            ];
        }

        return [
            $cancel,
            $this->getSaveFormAction()
                ->label('Salvar rascunho')
                ->icon('heroicon-m-document')
                ->color('gray'),
            Actions\Action::make('publish')
                ->label('Publicar aviso')
                ->icon('heroicon-m-paper-airplane')
                ->action(fn () => $this->publish()),
        ];
    }

    protected function getSavedNotification(): ?Notification
    {
        $notification = $this->alertSavedNotification($this->getRecord(), $this->savedTitle);

        if ($this->savedTitle === null && $this->getRecord()->is_active) {
            $notification->title('Alterações salvas');
        }

        return $notification;
    }
}
