<?php

namespace App\Filament\Resources\AlertResource\Pages;

use App\Filament\Resources\AlertResource;
use App\Filament\Resources\AlertResource\Concerns\InteractsWithAlertEditor;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateAlert extends CreateRecord
{
    use InteractsWithAlertEditor;

    protected static string $resource = AlertResource::class;

    /** Publicar (true) ou salvar como rascunho (false, padrão do Enter e do Ctrl+S) */
    protected bool $publishing = false;

    public function getTitle(): string
    {
        return 'Novo alerta';
    }

    public function getSubheading(): ?string
    {
        return 'Monte o alerta e acompanhe na prévia como ele aparece no site. Salve como rascunho para revisar depois ou publique para exibir.';
    }

    public function publish(): void
    {
        $this->publishing = true;

        $this->create();
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['is_active'] = $this->publishing;

        return $data;
    }

    protected function getFormActions(): array
    {
        return [
            $this->getCancelFormAction()->extraAttributes(['class' => 'al-cancel']),
            $this->getCreateFormAction()
                ->label('Salvar rascunho')
                ->icon('heroicon-m-document')
                ->color('gray'),
            Action::make('publish')
                ->label('Publicar aviso')
                ->icon('heroicon-m-paper-airplane')
                ->action(fn () => $this->publish()),
        ];
    }

    protected function getCreatedNotification(): ?Notification
    {
        return $this->alertSavedNotification($this->getRecord());
    }

    protected function getRedirectUrl(): string
    {
        // Publicado volta para a lista; rascunho continua no editor
        return $this->publishing
            ? static::getResource()::getUrl('index')
            : static::getResource()::getUrl('edit', ['record' => $this->getRecord()]);
    }
}
