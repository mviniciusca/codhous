<?php

namespace App\Services\Newsletter;

use App\Models\Setting;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

/**
 * Monta os dados da view mail.newsletter, usada tanto no envio quanto na prévia do painel.
 */
class NewsletterEmail
{
    /**
     * @param  string|TemporaryUploadedFile|null  $bannerPath  caminho no disco public ou upload ainda não salvo (prévia)
     */
    public static function viewData(string $subject, string $body, mixed $bannerPath = null, ?int $subscriberId = null, bool $preview = false): array
    {
        $website = Setting::get('website', []);
        $company = Setting::get('company', []);
        $brand = data_get($website, 'name') ?: data_get($company, 'trade_name') ?: config('app.name');

        return [
            'subject' => $subject,
            'bodyHtml' => NewsletterContent::forEmail(NewsletterContent::sanitize($body)),
            'preheader' => NewsletterContent::preheader($body),
            'brand' => $brand,
            'logo' => self::image(data_get($website, 'logo'), allowSvg: false),
            'banner' => self::image($bannerPath),
            'address' => self::address(data_get($company, 'address')),
            'unsubscribeUrl' => $preview ? '#' : self::unsubscribeUrl($subscriberId),
            'preview' => $preview,
        ];
    }

    public static function render(string $subject, string $body, mixed $bannerPath = null): string
    {
        return view('mail.newsletter', self::viewData($subject, $body, $bannerPath, preview: true))->render();
    }

    /** "Rua X, 10 · Bairro · Cidade/UF" (endereço exigido em comunicados por e-mail) */
    private static function address(mixed $address): ?string
    {
        if (! is_array($address)) {
            return is_string($address) && $address !== '' ? $address : null;
        }

        $street = trim(implode(', ', array_filter([data_get($address, 'street'), data_get($address, 'number')])));
        $city = trim(implode('/', array_filter([data_get($address, 'city'), data_get($address, 'state')])));

        return implode(' · ', array_filter([$street, data_get($address, 'neighborhood'), $city])) ?: null;
    }

    public static function unsubscribeUrl(?int $subscriberId): ?string
    {
        return $subscriberId ? URL::signedRoute('newsletter.unsubscribe', ['newsletter' => $subscriberId]) : null;
    }

    /**
     * Imagem para a view: 'path' (arquivo local, embutido no envio) e 'url' (prévia).
     * SVG fica de fora do logo porque Gmail e Outlook não exibem.
     *
     * @return array{path: ?string, url: ?string}|null
     */
    private static function image(mixed $value, bool $allowSvg = true): ?array
    {
        if (is_array($value)) {
            $value = reset($value) ?: null;
        }

        if ($value instanceof TemporaryUploadedFile) {
            return $value->isPreviewable() ? ['path' => null, 'url' => $value->temporaryUrl()] : null;
        }

        if (! is_string($value) || $value === '') {
            return null;
        }

        if (! $allowSvg && str_ends_with(strtolower($value), '.svg')) {
            return null;
        }

        $disk = Storage::disk('public');

        if (! $disk->exists($value)) {
            return null;
        }

        return ['path' => $disk->path($value), 'url' => $disk->url($value)];
    }
}
