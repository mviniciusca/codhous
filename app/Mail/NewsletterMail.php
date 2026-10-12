<?php

namespace App\Mail;

use App\Models\NewsletterCampaign;
use App\Models\NewsletterCampaignRecipient;
use App\Services\Newsletter\NewsletterEmail;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Headers;

/**
 * Comunicado da newsletter para um inscrito. Enviado pelo mailer padrão (SMTP configurado);
 * logo e imagem de topo vão embutidos (CID), então aparecem mesmo sem APP_URL público.
 */
class NewsletterMail extends Mailable
{
    public function __construct(
        public NewsletterCampaign $campaign,
        public ?NewsletterCampaignRecipient $recipient = null,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: $this->campaign->subject);
    }

    public function headers(): Headers
    {
        $url = NewsletterEmail::unsubscribeUrl($this->recipient?->newsletter_id);

        return new Headers(text: array_filter([
            'List-Unsubscribe' => $url ? "<{$url}>" : null,
            'List-Unsubscribe-Post' => $url ? 'List-Unsubscribe=One-Click' : null,
        ]));
    }

    public function content(): Content
    {
        return new Content(
            view: 'mail.newsletter',
            with: NewsletterEmail::viewData(
                subject: $this->campaign->subject,
                body: $this->campaign->body,
                bannerPath: $this->campaign->banner_path,
                subscriberId: $this->recipient?->newsletter_id,
            ),
        );
    }
}
