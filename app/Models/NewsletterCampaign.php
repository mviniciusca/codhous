<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Um disparo da newsletter. Os destinatários são copiados no momento da confirmação,
 * então quem se inscrever ou sair depois não muda este envio.
 */
class NewsletterCampaign extends Model
{
    public const STATUS_SENDING = 'sending';

    public const STATUS_SENT = 'sent';

    public const STATUS_PARTIAL = 'partial';

    public const STATUS_FAILED = 'failed';

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'recipients_count' => 'integer',
            'sent_count' => 'integer',
            'failed_count' => 'integer',
            'started_at' => 'datetime',
            'finished_at' => 'datetime',
        ];
    }

    public function recipients(): HasMany
    {
        return $this->hasMany(NewsletterCampaignRecipient::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isSending(): bool
    {
        return $this->status === self::STATUS_SENDING;
    }

    public function pendingCount(): int
    {
        return max(0, $this->recipients_count - $this->sent_count - $this->failed_count);
    }

    public function progress(): int
    {
        if ($this->recipients_count === 0) {
            return 100;
        }

        return (int) floor(($this->sent_count + $this->failed_count) * 100 / $this->recipients_count);
    }

    /** @return array<string, string> */
    public static function statusLabels(): array
    {
        return [
            self::STATUS_SENDING => 'Em andamento',
            self::STATUS_SENT => 'Enviado',
            self::STATUS_PARTIAL => 'Enviado com falhas',
            self::STATUS_FAILED => 'Falhou',
        ];
    }

    /** @return array<string, string> */
    public static function statusColors(): array
    {
        return [
            self::STATUS_SENDING => 'info',
            self::STATUS_SENT => 'success',
            self::STATUS_PARTIAL => 'warning',
            self::STATUS_FAILED => 'danger',
        ];
    }

    public function statusLabel(): string
    {
        return static::statusLabels()[$this->status] ?? $this->status;
    }
}
