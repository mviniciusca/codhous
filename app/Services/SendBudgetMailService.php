<?php

namespace App\Services;

use App\Models\Budget;
use App\Models\Mail as MailModel;
use App\Models\Setting;
use App\Services\PdfGeneratorService;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Mail;
use App\Mail\BudgetMail;

class SendBudgetMailService
{
    private ?string $pdfPath = null;

    public function __construct(
        private Budget $budget,
        private string $destinyEmail
    ) {}

    public function dispatch()
    {
        // 1. Garantir que o PDF existe ou gerá-lo
        $this->ensurePdfExists();

        // 2. Disparar e-mail via SMTP (Síncrono)
        $this->send();

        // 3. Registrar no histórico de e-mails
        $this->save();

        return $this->notification();
    }

    private function ensurePdfExists(): void
    {
        $settings = Setting::first();
        $company = $settings?->companySetting;
        $layout = $settings?->layoutSetting;

        // Gerar caminho do PDF
        $filename = 'budget-' . $this->budget->code . '-' . time() . '.pdf';
        $relativePath = 'budgets/' . $filename;
        $fullPath = storage_path('app/public/' . $relativePath);

        // Garantir diretório
        if (!file_exists(dirname($fullPath))) {
            mkdir(dirname($fullPath), 0755, true);
        }

        $pdfService = new PdfGeneratorService();
        $pdfService->saveFromView(
            'pdf.invoice',
            [
                'state' => $this->budget->toArray(),
                'budget' => $this->budget,
                'company' => $company,
                'layout' => $layout,
            ],
            $fullPath
        );

        $this->pdfPath = $fullPath;

        // Atualizar o orçamento com o novo PDF
        $this->budget->update(['pdf_document' => $relativePath]);
    }

    private function send(): void
    {
        $mailable = new BudgetMail($this->budget, $this->pdfPath);

        Mail::to($this->destinyEmail)->send($mailable);
    }

    private function save(): void
    {
        MailModel::create([
            'is_sent' => true,
            'name'    => data_get($this->budget->content, 'customer_name', 'Cliente'),
            'email'   => $this->destinyEmail,
            'subject' => "Orçamento #" . $this->budget->code,
            'message' => "Orçamento enviado com sucesso para " . $this->destinyEmail,
        ]);
    }

    private function notification()
    {
        return Notification::make()
            ->title('E-mail enviado!')
            ->body('O orçamento foi enviado com sucesso para ' . $this->destinyEmail)
            ->success()
            ->send();
    }
}
