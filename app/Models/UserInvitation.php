<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

/**
 * Convite para entrar na equipe. O banco guarda só o hash do token; o token em
 * claro existe apenas no link enviado por e-mail.
 */
class UserInvitation extends Model
{
    public const EXPIRES_IN_DAYS = 7;

    protected $fillable = ['email', 'name', 'role_id', 'invited_by'];

    protected $hidden = ['token_hash'];

    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime',
            'accepted_at' => 'datetime',
        ];
    }

    /**
     * Gera um token novo (invalida o link anterior) e renova a validade.
     * Devolve o token em claro para montar o link.
     */
    public function issueToken(): string
    {
        $token = Str::random(64);

        $this->forceFill([
            'token_hash' => self::hashToken($token),
            'expires_at' => now()->addDays(self::EXPIRES_IN_DAYS),
        ])->save();

        return $token;
    }

    public static function hashToken(string $token): string
    {
        return hash('sha256', $token);
    }

    /** Convite válido para o token informado, ou null */
    public static function findUsable(string $token): ?self
    {
        return static::query()
            ->pending()
            ->where('token_hash', self::hashToken($token))
            ->first();
    }

    public function url(string $token): string
    {
        return route('invitation.show', ['token' => $token]);
    }

    public function isExpired(): bool
    {
        return $this->expires_at?->isPast() ?? true;
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->whereNull('accepted_at')->where('expires_at', '>', now());
    }

    public function scopeOpen(Builder $query): Builder
    {
        return $query->whereNull('accepted_at');
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function inviter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'invited_by');
    }
}
