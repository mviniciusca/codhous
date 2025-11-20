<?php

namespace App\Services;

use App\Models\Budget;
use App\Models\Mail as MailModel;
use App\Models\Setting;
use App\Services\PdfGeneratorService;
use Closure;
use Filament\Notifications\Notification;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class SendBudgetMailService
{
    /**
     * Create a new class instance.
     */
    private string $code;

    private ?string $pdfPath;

    public function __construct(
        private array $state,
        private string $destinyEmailField,
        private Mailable $mailable)
    {
        $this->code = $this->state['code'];
        $this->pdfPath = null;
    }

    public function dispatch()
    {
        $this->generatePdf();
        $this->send();

        return $this->save();
    }

    private function generatePdf()
    {
        // Buscar dados necessários
        $budget = Budget::where('code', $this->code)->first();
        $company = Setting::first()->companySetting;
        $layout = Setting::first()->layoutSetting;
        
        // Aplicar fix de UTF-8 nos dados
        $budgetArray = $this->state;
        array_walk_recursive($budgetArray, function (&$item) {
            if (is_string($item)) {
                $item = mb_convert_encoding($item, 'UTF-8', 'UTF-8');
            }
        });

        // Garantir formato content[0]
        if (isset($budgetArray['content']) && !isset($budgetArray['content'][0])) {
            $budgetArray['content'] = [$budgetArray['content']];
        }

        // Gerar PDF
        $pdfService = new PdfGeneratorService();
        $filename = 'budget-' . $this->code . '-' . time() . '.pdf';
        $this->pdfPath = storage_path('app/public/budgets/' . $filename);

        $pdfService->saveFromView(
            'pdf.invoice',
            [
                'state' => $budgetArray,
                'budget' => $budget,
                'company' => $company,
                'layout' => $layout,
            ],
            $this->pdfPath
        );
    }

    private function send()
    {
        // Verificar se o PDF foi gerado corretamente antes de enviar
        if (! $this->pdfPath || ! file_exists($this->pdfPath)) {
            // Se não tiver o PDF, enviar sem anexo
            return Mail::to($this->destinyEmailField)
                ->send($this->mailable);
        }

        // Se for a classe BudgetMail, definimos a propriedade diretamente
        if ($this->mailable instanceof \App\Mail\BudgetMail) {
            $this->mailable->pdfPath = $this->pdfPath;
        }

        return Mail::to($this->destinyEmailField)
            ->send($this->mailable);
    }

    private function save()
    {
        MailModel::create([
            'is_sent' => true,
            'phone'   => null,
            'name'    => env('MAIL_FROM_NAME') ?? 'Codhous Software',
            'email'   => env('MAIL_FROM_ADDRESS'),
            'subject' => $this->subject(),
            'message' => $this->message(),
        ]);

        return $this->notification();
    }

    private function subject()
    {
        return "Budget Notification: {$this->code}";
    }

    private function message()
    {
        return "The document with Budget ID: #{$this->code} was sent to customer email: {$this->destinyEmailField} with success!";
    }

    private function notification()
    {
        return Notification::make()
            ->title(__('Document was sent via email with success.'))
            ->success()
            ->send();
    }
}
