<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class TurnstileService
{
    protected string $secretKey;
    protected string $siteKey;
    protected bool $enabled;

    public function __construct()
    {
        $this->enabled = \App\Models\Setting::get('security.turnstile.enabled', false);
        $this->siteKey = \App\Models\Setting::get('security.turnstile.site_key', '');
        $this->secretKey = \App\Models\Setting::get('security.turnstile.secret_key', '');
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    public function getSiteKey(): string
    {
        return $this->siteKey;
    }

    /**
     * Verifica o token enviado pelo widget do Cloudflare Turnstile.
     *
     * @param string|null $token
     * @param string|null $remoteIp
     * @return bool
     */
    public function verify(?string $token, ?string $remoteIp = null): bool
    {
        // Se estiver desativado no painel, ignoramos a verificação
        if (!$this->enabled) {
            return true;
        }

        if (empty($token)) {
            return false;
        }

        $response = Http::asForm()->post('https://challenges.cloudflare.com/turnstile/v0/siteverify', [
            'secret' => $this->secretKey,
            'response' => $token,
            'remoteip' => $remoteIp,
        ]);

        return $response->successful() && $response->json('success');
    }
}
