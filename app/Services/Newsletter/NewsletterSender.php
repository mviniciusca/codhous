<?php

namespace App\Services\Newsletter;

use App\Mail\NewsletterMail;
use App\Models\Newsletter;
use App\Models\NewsletterCampaign;
use App\Models\NewsletterCampaignRecipient;
use App\Models\User;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use Throwable;

/**
 * Disparo da newsletter pelo SMTP configurado, sem fila: a tela de envio chama
 * processBatch() em sequência (poll do Livewire) e cada chamada envia um lote pequeno.
 * Assim o envio funciona sem worker, mostra o progresso e pode ser retomado.
 *
 * Proteções contra envio duplicado:
 * - chave de idempotência única por confirmação (clique duplo não cria outro envio);
 * - um destinatário por e-mail em cada envio e trava por envio (duas abas não mandam em dobro);
 * - bloqueio de conteúdo idêntico enquanto outro envio dele está em andamento.
 */
class NewsletterSender
{
    public const BATCH_SIZE = 10;

    /** Janela em que um conteúdo idêntico já enviado exige confirmação extra */
    public const DUPLICATE_WINDOW_HOURS = 24;

    public static function activeSubscribers()
    {
        return Newsletter::query()->where('is_active', true);
    }

    public static function contentHash(string $subject, string $body, ?string $bannerPath): string
    {
        $disk = Storage::disk('public');
        $banner = $bannerPath && $disk->exists($bannerPath) ? hash('sha256', $disk->get($bannerPath)) : null;

        return NewsletterContent::hash($subject, NewsletterContent::sanitize($body), $banner);
    }

    /** Envio recente (ou em andamento) com o mesmo conteúdo */
    public static function recentDuplicate(string $hash): ?NewsletterCampaign
    {
        return NewsletterCampaign::query()
            ->where('content_hash', $hash)
            ->where('created_at', '>=', now()->subHours(self::DUPLICATE_WINDOW_HOURS))
            ->latest('id')
            ->first();
    }

    /**
     * Cria o envio e copia os inscritos ativos. Repetir a mesma chave devolve o envio já criado.
     *
     * @param  array{subject: string, body: string, banner?: ?string}  $data
     */
    public function create(array $data, string $idempotencyKey, ?User $user = null): NewsletterCampaign
    {
        if ($existing = NewsletterCampaign::where('idempotency_key', $idempotencyKey)->first()) {
            return $existing;
        }

        $subject = trim($data['subject']);
        $body = NewsletterContent::sanitize($data['body']);
        $banner = $data['banner'] ?? null;

        if ($subject === '' || NewsletterContent::isBlank($body)) {
            throw new RuntimeException('Preencha o assunto e a mensagem antes de enviar.');
        }

        $hash = self::contentHash($subject, $body, $banner);

        if (NewsletterCampaign::where('content_hash', $hash)->where('status', NewsletterCampaign::STATUS_SENDING)->exists()) {
            throw new RuntimeException('Esta mesma mensagem já está sendo enviada. Acompanhe o envio no histórico.');
        }

        try {
            return DB::transaction(function () use ($subject, $body, $banner, $hash, $idempotencyKey, $user) {
                $campaign = NewsletterCampaign::create([
                    'idempotency_key' => $idempotencyKey,
                    'subject' => $subject,
                    'body' => $body,
                    'banner_path' => $banner,
                    'content_hash' => $hash,
                    'status' => NewsletterCampaign::STATUS_SENDING,
                    'user_id' => $user?->getKey(),
                    'started_at' => now(),
                ]);

                $now = now();
                $emails = [];

                self::activeSubscribers()->orderBy('id')->chunkById(500, function ($subscribers) use ($campaign, $now, &$emails) {
                    $rows = [];

                    foreach ($subscribers as $subscriber) {
                        $email = mb_strtolower(trim($subscriber->email));

                        if (isset($emails[$email]) || ! filter_var($email, FILTER_VALIDATE_EMAIL)) {
                            continue;
                        }

                        $emails[$email] = true;
                        $rows[] = [
                            'newsletter_campaign_id' => $campaign->id,
                            'newsletter_id' => $subscriber->id,
                            'email' => $email,
                            'name' => $subscriber->name,
                            'status' => NewsletterCampaignRecipient::STATUS_PENDING,
                            'created_at' => $now,
                            'updated_at' => $now,
                        ];
                    }

                    NewsletterCampaignRecipient::insert($rows);
                });

                if ($emails === []) {
                    throw new RuntimeException('Não há inscritos ativos para receber esta mensagem.');
                }

                $campaign->update(['recipients_count' => count($emails)]);

                return $campaign;
            });
        } catch (UniqueConstraintViolationException) {
            // Duas requisições com a mesma chave ao mesmo tempo: fica a que gravou primeiro
            return NewsletterCampaign::where('idempotency_key', $idempotencyKey)->firstOrFail();
        }
    }

    /** Envia o próximo lote. Devolve o envio atualizado; se outra requisição já está enviando, só lê. */
    public function processBatch(NewsletterCampaign $campaign, int $size = self::BATCH_SIZE): NewsletterCampaign
    {
        if (! $campaign->isSending()) {
            return $campaign;
        }

        Cache::lock("newsletter-campaign:{$campaign->id}", 120)->get(function () use ($campaign, $size) {
            // Requisição que caiu no meio do envio: não reenvia às cegas, registra como falha para revisão
            $campaign->recipients()
                ->where('status', NewsletterCampaignRecipient::STATUS_SENDING)
                ->where('updated_at', '<', now()->subMinutes(5))
                ->update([
                    'status' => NewsletterCampaignRecipient::STATUS_FAILED,
                    'error' => 'O envio foi interrompido antes da confirmação do servidor. A pessoa pode ter recebido.',
                ]);

            $recipients = $campaign->recipients()
                ->where('status', NewsletterCampaignRecipient::STATUS_PENDING)
                ->orderBy('id')
                ->limit($size)
                ->get();

            foreach ($recipients as $recipient) {
                $this->sendTo($campaign, $recipient);
            }

            $this->refresh($campaign);
        });

        return $campaign->refresh();
    }

    /** Volta as falhas para a fila deste envio */
    public function retryFailed(NewsletterCampaign $campaign): NewsletterCampaign
    {
        if ($campaign->isSending() || $campaign->failed_count === 0) {
            return $campaign;
        }

        $campaign->recipients()
            ->where('status', NewsletterCampaignRecipient::STATUS_FAILED)
            ->update(['status' => NewsletterCampaignRecipient::STATUS_PENDING]);

        $campaign->update(['status' => NewsletterCampaign::STATUS_SENDING, 'finished_at' => null]);
        $this->refresh($campaign);

        return $campaign->refresh();
    }

    private function sendTo(NewsletterCampaign $campaign, NewsletterCampaignRecipient $recipient): void
    {
        // Marca antes de enviar: se a requisição cair no meio, o destinatário não recebe de novo
        $claimed = NewsletterCampaignRecipient::whereKey($recipient->id)
            ->where('status', NewsletterCampaignRecipient::STATUS_PENDING)
            ->update(['status' => NewsletterCampaignRecipient::STATUS_SENDING, 'attempts' => DB::raw('attempts + 1')]);

        if (! $claimed) {
            return;
        }

        try {
            Mail::to($recipient->email, $recipient->name)->send(new NewsletterMail($campaign, $recipient));

            $recipient->forceFill(['status' => NewsletterCampaignRecipient::STATUS_SENT, 'error' => null, 'sent_at' => now()])->save();
        } catch (Throwable $e) {
            Log::warning('Falha no envio da newsletter', ['campaign' => $campaign->id, 'recipient' => $recipient->id, 'error' => $e->getMessage()]);

            $recipient->forceFill(['status' => NewsletterCampaignRecipient::STATUS_FAILED, 'error' => self::friendlyError($e)])->save();
        }
    }

    private function refresh(NewsletterCampaign $campaign): void
    {
        $counts = $campaign->recipients()
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $sent = (int) ($counts[NewsletterCampaignRecipient::STATUS_SENT] ?? 0);
        $failed = (int) ($counts[NewsletterCampaignRecipient::STATUS_FAILED] ?? 0);
        $open = (int) ($counts[NewsletterCampaignRecipient::STATUS_PENDING] ?? 0) + (int) ($counts[NewsletterCampaignRecipient::STATUS_SENDING] ?? 0);

        $attributes = ['sent_count' => $sent, 'failed_count' => $failed];

        if ($open === 0) {
            $attributes['status'] = match (true) {
                $failed === 0 => NewsletterCampaign::STATUS_SENT,
                $sent === 0 => NewsletterCampaign::STATUS_FAILED,
                default => NewsletterCampaign::STATUS_PARTIAL,
            };
            $attributes['finished_at'] = now();
        }

        $campaign->update($attributes);
    }

    /** Mensagem curta para o histórico, sem expor credenciais ou detalhes do servidor */
    private static function friendlyError(Throwable $e): string
    {
        $message = $e->getMessage();

        $hint = match (true) {
            str_contains($message, 'Connection could not be established'), str_contains($message, 'Connection refused') => 'Não foi possível conectar ao servidor SMTP.',
            str_contains($message, 'authenticate'), str_contains($message, 'Authentication') => 'O servidor SMTP recusou o usuário ou a senha.',
            (bool) preg_match('/\b55\d\b/', $message) => 'O servidor recusou o destinatário (endereço inexistente ou bloqueado).',
            (bool) preg_match('/\b45\d\b/', $message) => 'O servidor pediu para tentar mais tarde (limite temporário).',
            default => 'O servidor de e-mail recusou o envio.',
        };

        $detail = (string) str(preg_replace('/\s+/', ' ', strip_tags($message)))->limit(180);

        return $detail !== '' ? "{$hint} ({$detail})" : $hint;
    }
}
