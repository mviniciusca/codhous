<?php

namespace App\Filament\Resources\BudgetResource\Pages;

use App\Filament\Resources\BudgetResource;
use App\Models\Setting;
use App\Services\PdfGeneratorService;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditBudget extends EditRecord
{
    protected static string $resource = BudgetResource::class;

    /** Quantas entradas do histórico estão visíveis */
    public int $timelineLimit = 15;

    public function getTitle(): string
    {
        return 'Orçamento '.$this->record->code;
    }

    public function getSubheading(): ?string
    {
        $customer = $this->record->content['customer_name'] ?? 'Cliente não informado';
        $origin = $this->record->user_id ? 'criado no painel' : 'recebido pelo site';
        $date = $this->record->created_at?->timezone(config('app.display_timezone'))->format('d/m/Y \à\s H:i');

        return "{$customer} · {$origin} em {$date}";
    }

    /**
     * Chamado pela barra de etapas (resources/views/filament/budget/status-pipeline.blade.php).
     */
    public function setStatus(string $status): void
    {
        if (! array_key_exists($status, BudgetResource::STATUSES) || $this->record->status === $status) {
            return;
        }

        $this->record->update(['status' => $status]);

        Notification::make()
            ->title('Etapa: '.BudgetResource::STATUSES[$status])
            ->success()
            ->send();
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $fieldsToFormat = ['tax', 'discount', 'subtotal', 'shipping', 'total'];

        if (isset($data['content']) && is_array($data['content'])) {
            // Pedidos do site (e antigos) não têm taxa/desconto; sem isso o formulário não salva
            foreach (['tax', 'discount', 'shipping'] as $field) {
                if (! isset($data['content'][$field]) || $data['content'][$field] === '') {
                    $data['content'][$field] = 0;
                }
            }

            foreach ($fieldsToFormat as $field) {
                if (isset($data['content'][$field])) {
                    $val = $data['content'][$field];
                    if (is_numeric($val) && ! str_contains((string) $val, ',')) {
                        $data['content'][$field] = number_format(floatval($val), 2, ',', '.');
                    }
                }
            }
        }

        return $data;
    }

    public function loadMoreTimeline(): void
    {
        $this->timelineLimit += 15;
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('download_pdf')
                ->label('Baixar PDF')
                ->icon('heroicon-o-document-arrow-down')
                ->color('gray')
                ->tooltip('Salva e baixa o PDF com os valores atuais')
                ->action(function () {
                    $this->save(shouldRedirect: false, shouldSendSavedNotification: false);

                    $budget = $this->record->refresh();
                    $settings = Setting::first();
                    $output = (new PdfGeneratorService)->outputFromView('pdf.invoice', [
                        'state' => $budget->toArray(),
                        'budget' => $budget,
                        'company' => $settings?->companySetting,
                        'layout' => $settings?->layoutSetting,
                    ]);

                    return response()->streamDownload(fn () => print ($output), 'Orcamento_'.$budget->code.'.pdf', [
                        'Content-Type' => 'application/pdf',
                    ]);
                }),
            Actions\Action::make('save')
                ->label('Salvar')
                ->icon('heroicon-o-check')
                ->action('save')
                ->color('primary')
                ->keyBindings(['mod+s']),
            Actions\ActionGroup::make([
                Actions\DeleteAction::make()
                    ->label('Mover para lixeira')
                    ->modalHeading('Mover orçamento para a lixeira?')
                    ->successNotificationTitle('Orçamento enviado para a lixeira'),
                Actions\RestoreAction::make()
                    ->label('Restaurar'),
                Actions\ForceDeleteAction::make()
                    ->label('Excluir permanentemente'),
            ])
                ->icon('heroicon-m-ellipsis-vertical')
                ->color('gray')
                ->tooltip('Mais ações'),
        ];
    }

    protected function getFormActions(): array
    {
        return [];
    }
}
