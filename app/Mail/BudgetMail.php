<?php

namespace App\Mail;

use App\Models\Budget;
use App\Models\Setting;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Queue\SerializesModels;

class BudgetMail extends Mailable
{
    use Queueable, SerializesModels;

    public $company;
    public $layout;

    /**
     * Create a new message instance.
     */
    public function __construct(public Budget $budget, public ?string $pdfPath = null)
    {
        $settings = Setting::first();
        $this->company = $settings?->companySetting ?? (object)[];
        $this->layout = $settings?->layoutSetting ?? (object)[];
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Orçamento #{$this->budget->code} - " . ($this->company->trade_name ?? config('app.name')),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'mail.budget',
            with: [
                'budget' => $this->budget,
                'company' => $this->company,
                'layout' => $this->layout,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, Attachment>
     */
    public function attachments(): array
    {
        if ($this->pdfPath && file_exists($this->pdfPath)) {
            return [
                Attachment::fromPath($this->pdfPath)
                    ->as('orcamento-' . $this->budget->code . '.pdf')
                    ->withMime('application/pdf'),
            ];
        }

        return [];
    }
}
