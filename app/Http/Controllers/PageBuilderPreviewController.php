<?php

namespace App\Http\Controllers;

use App\Enums\AccessArea;
use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

/**
 * Renderiza a página completa (header, blocos e footer) a partir do snapshot
 * gerado pelo Page Builder, para ser exibida no live preview do painel.
 */
class PageBuilderPreviewController extends Controller
{
    public const CACHE_PREFIX = 'page-builder-preview:';

    public const TTL_MINUTES = 60;

    public function __invoke(Request $request, string $token)
    {
        $payload = Cache::get(self::CACHE_PREFIX.$token);

        // Rotas fora do Filament: a área precisa ser conferida aqui também
        abort_unless($request->user()?->canAccessArea(AccessArea::Website), 403);

        // O snapshot só pode ser visto pelo mesmo usuário que o gerou.
        abort_unless(
            is_array($payload) && hash_equals((string) ($payload['user_id'] ?? ''), (string) $request->user()?->getAuthIdentifier()),
            404,
        );

        $blocks = $payload['blocks'] ?? [];

        $page = new Page([
            'title' => $payload['title'] ?? 'Pré-visualização',
            'slug' => $payload['slug'] ?? 'preview',
            'meta' => $payload['meta'] ?? [],
            'content' => array_values($blocks),
        ]);

        return response()
            ->view('page', [
                'page' => $page,
                'meta' => [
                    'title' => data_get($payload, 'meta.title') ?: $page->title,
                    'description' => data_get($payload, 'meta.description'),
                    'keywords' => data_get($payload, 'meta.keywords'),
                    'og_image' => null,
                ],
                'builderPreview' => true,
                'blockKeys' => array_map('strval', array_keys($blocks)),
                'blockLabels' => $payload['labels'] ?? [],
            ])
            ->header('Cache-Control', 'no-store, private')
            ->header('X-Robots-Tag', 'noindex, nofollow')
            ->header('X-Frame-Options', 'SAMEORIGIN');
    }
}
