<?php

namespace App\Services;

use App\Models\Mail as MailModel;
use Closure;
use Filament\Notifications\Notification;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\Mail;

class SendBudgetMail
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
        $pdfGenerator = new PdfGenerator($this->state);
        $pdfGenerator->generate();
        $this->pdfPath = $pdfGenerator->filePath;
    }

    private function send()
    {
        // Verificar se o PDF foi gerado corretamente antes de enviar
        if (! $this->pdfPath || ! file_exists($this->pdfPath)) {
            // Se não tiver o PDF, enviar sem anexo
            return Mail::to($this->destinyEmailField)
                ->send($this->mailable);
        }

        // Como estamos usando a classe BudgetMail com a API fluente,
        // precisamos passar o caminho do PDF para o construtor ou para um método set
        if (method_exists($this->mailable, 'pdfPath')) {
            // Se existir um setter, usamos ele
            $this->mailable->pdfPath($this->pdfPath);
        } elseif ($this->mailable instanceof \App\Mail\BudgetMail) {
            // Se for a classe BudgetMail, definimos a propriedade diretamente
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
