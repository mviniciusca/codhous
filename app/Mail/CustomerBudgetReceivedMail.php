<?php

namespace App\Mail;

use App\Models\Budget;
use App\Models\Setting;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Confirmação enviada ao cliente logo após ele solicitar um orçamento pelo site.
 */
class CustomerBudgetReceivedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $company;

    public function __construct(public Budget $budget)
    {
        $this->company = (object) (Setting::get('company') ?? []);
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Recebemos sua solicitação de orçamento #{$this->budget->code}",
        );
    }

    public function content(): Content
    {
        $this->budget->loadMissing('budgetItems.product', 'budgetItems.productOption');

        return new Content(
            view: 'mail.customer-budget-received',
            with: [
                'budget' => $this->budget,
                'company' => $this->company,
                'customerName' => data_get($this->budget->content, 'customer_name', 'Cliente'),
            ],
        );
    }
}
