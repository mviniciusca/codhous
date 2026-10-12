<?php

namespace App\Http\Controllers;

use App\Models\Page;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

/**
 * Renderiza uma página real do site com as configurações ainda não salvas
 * da tela Design e SEO, para a pré-visualização do painel.
 */
class DesignPreviewController extends Controller
{
    public const CACHE_PREFIX = 'design-preview:';

    public const TTL_MINUTES = 60;

    public function __invoke(Request $request, string $token)
    {
        $payload = Cache::get(self::CACHE_PREFIX.$token);

        // O snapshot só pode ser visto pelo mesmo usuário que o gerou.
        abort_unless(
            is_array($payload) && hash_equals((string) ($payload['user_id'] ?? ''), (string) $request->user()?->getAuthIdentifier()),
            404,
        );

        Setting::previewWith($payload['settings'] ?? []);

        try {
            return $this->render($request);
        } finally {
            // A view já foi renderizada no response; não deixa a prévia vazar para outra requisição
            Setting::endPreview();
        }
    }

    protected function render(Request $request)
    {
        $slug = trim((string) $request->query('pagina', '/'), '/') ?: '/';

        $page = Page::where('slug', $slug)->visible()->first()
            ?? Page::where('slug', '/')->visible()->first()
            ?? Page::visible()->orderBy('sort_order')->first();

        abort_unless($page, 404, 'Nenhuma página publicada para pré-visualizar.');

        return response()
            ->view('page', [
                'page' => $page,
                'meta' => [
                    'title' => data_get($page->meta, 'title') ?: $page->title,
                    'description' => data_get($page->meta, 'description'),
                    'keywords' => data_get($page->meta, 'keywords'),
                    'og_image' => data_get($page->meta, 'og_image'),
                ],
                'sitePreview' => true,
            ])
            ->header('Cache-Control', 'no-store, private')
            ->header('X-Robots-Tag', 'noindex, nofollow')
            ->header('X-Frame-Options', 'SAMEORIGIN');
    }
}
