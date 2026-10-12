<?php

namespace App\Services\PosterStudio;

use App\Models\ContentSection;
use App\Models\Equipment;
use App\Models\Page;
use App\Models\Product;
use App\Models\Setting;
use App\Services\WhatsappService;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Identidade e dados comerciais da empresa, lidos dos Settings e do catálogo.
 * Elementos do documento podem se vincular às chaves de bindings() (ex.:
 * "brand.phone"); nada aqui é inventado: um dado ausente fica null.
 */
class BrandKit
{
    public const BINDINGS = [
        'brand.name' => 'Nome da empresa',
        'brand.phone' => 'Telefone',
        'brand.whatsapp' => 'WhatsApp',
        'brand.whatsapp_url' => 'Link do WhatsApp',
        'brand.email' => 'E-mail',
        'brand.website' => 'Site',
        'brand.instagram' => 'Instagram',
        'brand.address' => 'Endereço',
        'brand.city' => 'Cidade',
        'brand.hours' => 'Horário de atendimento',
        'brand.logo' => 'Logo',
        'brand.mascot' => 'Mascote',
        'brand.photo' => 'Foto principal',
    ];

    public const IMAGE_BINDINGS = ['brand.logo', 'brand.mascot', 'brand.photo'];

    private ?array $bindings = null;

    private ?array $photos = null;

    /**
     * @return array<string, ?string>
     */
    public function bindings(): array
    {
        if ($this->bindings !== null) {
            return $this->bindings;
        }

        $phone = $this->clean(Setting::get('company.phone'));
        $whatsappUrl = $this->whatsappUrl($phone);
        $photos = $this->photos();

        return $this->bindings = [
            'brand.name' => $this->clean(Setting::get('company.trade_name') ?? Setting::get('website.name')),
            'brand.phone' => $phone,
            'brand.whatsapp' => $this->whatsappDisplay($whatsappUrl) ?? $phone,
            'brand.whatsapp_url' => $whatsappUrl,
            'brand.email' => $this->clean(Setting::get('company.email')),
            'brand.website' => $this->websiteHost(),
            'brand.instagram' => $this->instagramHandle(),
            'brand.address' => $this->address(),
            'brand.city' => $this->city(),
            'brand.hours' => $this->clean(Setting::get('company.opening_hours')),
            'brand.logo' => $this->publicUrl(Setting::get('website.logo')),
            'brand.mascot' => $this->publicUrl(Setting::get('website.mascot')),
            'brand.photo' => collect($photos)->firstWhere('kind', 'photo')['url'] ?? ($photos[0]['url'] ?? null),
        ];
    }

    public function value(string $key): ?string
    {
        return $this->bindings()[$key] ?? null;
    }

    /**
     * Fotos da empresa já usadas no site (blocos de página, seções e equipamentos).
     * Somente arquivos locais: imagens externas impediriam a exportação do canvas.
     *
     * @return array<int, array{url:string,name:string,kind:string,source:string}>
     */
    public function photos(): array
    {
        if ($this->photos !== null) {
            return $this->photos;
        }

        $paths = collect();

        $collect = function ($value) use (&$collect, $paths) {
            if (is_array($value)) {
                array_walk($value, $collect);
            } elseif (is_string($value) && preg_match('/^[\w\-\/]+\.(jpe?g|png|webp)$/i', $value)) {
                $paths->push($value);
            }
        };

        Page::query()->get(['content'])->each(fn ($page) => $collect($page->content ?? []));
        ContentSection::query()->get(['content'])->each(fn ($section) => $collect($section->content ?? []));

        $disk = Storage::disk('public');
        $exclude = array_filter([Setting::get('website.logo'), Setting::get('website.mascot'), Setting::get('website.favicon')]);

        $site = $paths->unique()
            ->reject(fn ($path) => in_array($path, $exclude, true) || Str::startsWith($path, ['partners/', 'avatars/', 'sections/payment/']))
            ->filter(fn ($path) => $disk->exists($path))
            ->sortBy(fn ($path) => preg_match('/\.jpe?g$/i', $path) ? 0 : 1)
            ->take(40)
            ->map(fn ($path) => [
                'url' => self::relativeUrl($disk->url($path)),
                'name' => basename($path),
                'kind' => preg_match('/\.jpe?g$/i', $path) ? 'photo' : 'graphic',
                'source' => 'Site',
            ]);

        $equipment = Equipment::query()->whereNotNull('image')->get()
            ->filter(fn ($item) => $disk->exists($item->image))
            ->map(fn ($item) => [
                'url' => self::relativeUrl($disk->url($item->image)),
                'name' => $item->name,
                'kind' => 'graphic',
                'source' => 'Equipamentos',
            ]);

        return $this->photos = $site->concat($equipment)->values()->all();
    }

    /**
     * Produtos ativos com preços, para inserir no post sem digitar.
     */
    public function products(): array
    {
        return Product::query()
            ->where('is_active', true)
            ->with(['productOption' => fn ($query) => $query->where('is_active', true)->orderBy('price')])
            ->orderBy('name')
            ->get()
            ->map(fn (Product $product) => [
                'id' => $product->id,
                'name' => $product->name,
                'description' => $product->description,
                'options' => $product->productOption->map(fn ($option) => [
                    'id' => $option->id,
                    'name' => $option->name,
                    'unit' => $option->unit,
                    'price' => (float) $option->price,
                    'price_formatted' => 'R$ '.number_format((float) $option->price, 2, ',', '.'),
                ])->values()->all(),
            ])
            ->all();
    }

    public function equipment(): array
    {
        $disk = Storage::disk('public');

        return Equipment::query()->orderBy('name')->get()
            ->map(fn (Equipment $item) => [
                'id' => $item->id,
                'name' => $item->name,
                'category' => $item->category,
                'description' => $item->description,
                'available' => (bool) $item->is_available,
                'image' => $item->image && $disk->exists($item->image) ? self::relativeUrl($disk->url($item->image)) : null,
            ])
            ->all();
    }

    /**
     * Substitui {{brand.*}} em textos livres (ex.: legenda). Chaves sem valor somem.
     */
    public function interpolate(string $text): string
    {
        $bindings = $this->bindings();

        $result = preg_replace_callback('/\{\{\s*(brand\.[a-z_]+)\s*\}\}/', fn ($m) => $bindings[$m[1]] ?? '', $text);

        return trim(preg_replace("/[ \t]+\n/", "\n", $result));
    }

    public function toArray(): array
    {
        return [
            'bindings' => $this->bindings(),
            'labels' => self::BINDINGS,
            'imageBindings' => self::IMAGE_BINDINGS,
            'photos' => $this->photos(),
            'products' => $this->products(),
            'equipment' => $this->equipment(),
        ];
    }

    private function clean(mixed $value): ?string
    {
        return is_string($value) && trim($value) !== '' ? trim($value) : null;
    }

    private function publicUrl(mixed $path): ?string
    {
        if (! is_string($path) || $path === '' || ! Storage::disk('public')->exists($path)) {
            return null;
        }

        return self::relativeUrl(Storage::disk('public')->url($path));
    }

    /**
     * URLs de mídia são guardadas relativas ao site para que o canvas nunca
     * carregue imagens de outra origem (o que bloquearia a exportação).
     */
    public static function relativeUrl(string $url): string
    {
        return parse_url($url, PHP_URL_PATH) ?: $url;
    }

    private function whatsappUrl(?string $phone): ?string
    {
        $configured = $this->clean(Setting::get('website.social_networks.whatsapp'));

        if ($configured && Str::startsWith($configured, ['http://', 'https://'])) {
            return $configured;
        }

        $number = $configured ?? $phone;

        return $number ? app(WhatsappService::class)->generateUrl($number, 'Olá! Vi a publicação de vocês e gostaria de mais informações.') : null;
    }

    /**
     * "https://wa.me/5521900000000" → "(21) 90000-0000".
     */
    private function whatsappDisplay(?string $url): ?string
    {
        if (! $url || ! preg_match('/wa\.me\/(\d+)/', $url, $m)) {
            return null;
        }

        $digits = preg_replace('/^55(?=\d{10,11}$)/', '', $m[1]);

        return match (strlen($digits)) {
            11 => sprintf('(%s) %s-%s', substr($digits, 0, 2), substr($digits, 2, 5), substr($digits, 7)),
            10 => sprintf('(%s) %s-%s', substr($digits, 0, 2), substr($digits, 2, 4), substr($digits, 6)),
            default => null,
        };
    }

    private function websiteHost(): ?string
    {
        $host = parse_url((string) config('app.url'), PHP_URL_HOST);

        if (! $host || in_array($host, ['localhost', '127.0.0.1'], true)) {
            return null;
        }

        return preg_replace('/^www\./', '', $host);
    }

    private function instagramHandle(): ?string
    {
        $value = $this->clean(Setting::get('website.social_networks.instagram'));

        if (! $value) {
            return null;
        }

        $handle = trim((string) (parse_url($value, PHP_URL_PATH) ?? $value), '/@ ');

        return $handle !== '' ? '@'.Str::before($handle, '/') : null;
    }

    private function address(): ?string
    {
        $address = Setting::get('company.address');

        if (! is_array($address)) {
            return $this->clean($address);
        }

        $line = trim(implode(', ', array_filter([$address['street'] ?? null, $address['number'] ?? null])));
        $parts = array_filter([$line, $address['neighborhood'] ?? null, $this->city()]);

        return $parts ? implode(' - ', $parts) : null;
    }

    private function city(): ?string
    {
        $address = Setting::get('company.address');

        if (! is_array($address) || empty($address['city'])) {
            return null;
        }

        return trim($address['city'].(! empty($address['state']) ? '/'.$address['state'] : ''));
    }
}
