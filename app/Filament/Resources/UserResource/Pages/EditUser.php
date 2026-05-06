<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use App\Filament\Resources\UserResource\Widgets\UserStatsWidget;
use App\Models\User;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    public function getTitle(): string
    {
        return 'Visão Geral e Gestão do Usuário';
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->label('Excluir Usuário')
                ->modalHeading('Excluir conta de usuário?')
                ->modalDescription('Esta ação é permanente e removerá todos os acessos deste membro da equipe. Tem certeza?')
                ->modalSubmitActionLabel('Sim, excluir permanentemente')
                ->hidden(fn (User $record) => $record->id === auth()->id() || $record->id === 1) // Impede deletar a si mesmo ou o admin ID 1
                ->before(function (Actions\DeleteAction $action, User $record) {
                    if ($record->id === 1) {
                        Notification::make()
                            ->title('Ação Bloqueada')
                            ->body('O administrador principal do sistema não pode ser excluído.')
                            ->danger()
                            ->send();

                        $action->halt();
                    }
                }),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            UserStatsWidget::class,
        ];
    }
}
