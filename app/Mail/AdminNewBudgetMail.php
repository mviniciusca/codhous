<?php

namespace App\Mail;

use App\Models\Budget;
use App\Models\Setting;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AdminNewBudgetMail extends Mailable
{
    use Queueable, SerializesModels;

    public $company;

    /**
     * Create a new message instance.
     */
    public function __construct(public Budget $budget)
    {
        $this->company = Setting::first()?->companySetting;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "🔔 Novo Orçamento Recebido: #{$this->budget->code} - " . data_get($this->budget->content, 'customer_name', 'Cliente'),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'mail.admin-new-budget',
            with: [
                'budget' => $this->budget,
                'company' => $this->company,
                'customerName' => data_get($this->budget->content, 'customer_name', 'Cliente'),
                'customerPhone' => data_get($this->budget->content, 'customer_phone', '-'),
                'totalValue' => data_get($this->budget->content, 'total', 0),
                'adminUrl' => url("/admin/budgets/{$this->budget->id}/edit"),
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
