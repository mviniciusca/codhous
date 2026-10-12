<?php

namespace App\Http\Controllers;

use App\Enums\AccessArea;
use App\Models\ContentSection;
use App\Models\Page;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Cache;

/**
 * Renderiza uma única seção (ContentSection) a partir do snapshot não salvo
 * do formulário, para o live preview da criação/edição de seções.
 */
class ContentSectionPreviewController extends Controller
{
    public const CACHE_PREFIX = 'content-section-preview:';

    public const TTL_MINUTES = 60;

    public function __invoke(Request $request, string $token)
    {
        // Rota fora do Filament: a área precisa ser conferida aqui também
        abort_unless($request->user()?->canAccessArea(AccessArea::Website), 403);

        $payload = Cache::get(self::CACHE_PREFIX.$token);

        // O snapshot só pode ser visto pelo mesmo usuário que o gerou
        abort_unless(
            is_array($payload) && hash_equals((string) ($payload['user_id'] ?? ''), (string) $request->user()?->getAuthIdentifier()),
            404,
        );

        $type = (string) ($payload['type'] ?? '');

        abort_unless(array_key_exists($type, ContentSection::typeLabels()), 404);

        $data = (array) ($payload['content'] ?? []);
        $label = ContentSection::typeLabels()[$type];
        [$html, $error] = $this->renderSection($type, $data);

        return response()
            ->view('section-preview', [
                'label' => $label,
                'html' => $html,
                'error' => $error,
                'chrome' => $request->boolean('chrome'),
            ])
            ->header('Cache-Control', 'no-store, private')
            ->header('X-Robots-Tag', 'noindex, nofollow')
            ->header('X-Frame-Options', 'SAMEORIGIN');
    }

    /**
     * Renderiza o bloco antes do layout: uma seção incompleta vira mensagem de erro
     * em vez de derrubar a página da prévia.
     *
     * @return array{0: string, 1: ?string}
     */
    protected function renderSection(string $type, array $data): array
    {
        try {
            $html = Blade::render(
                '<x-render-block :type="$type" :data="$data" :page="$page" :theme="$theme" />',
                [
                    'type' => $type,
                    'data' => $data,
                    // Página "home" fictícia: evita o cabeçalho automático de páginas internas
                    'page' => new Page(['title' => 'Pré-visualização', 'slug' => '/', 'content' => []]),
                    'theme' => data_get(Setting::get('website', []), 'theme', 'default'),
                ],
            );

            return [$html, null];
        } catch (\Throwable $e) {
            // Um componente que falhou no meio deixa a pilha de componentes do Blade inconsistente
            app('view')->flushState();

            return ['', $e->getMessage()];
        }
    }
}
