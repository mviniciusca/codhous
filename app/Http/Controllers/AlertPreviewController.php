<?php

namespace App\Http\Controllers;

use App\Enums\AccessArea;
use App\Models\Alert;
use App\Models\Page;
use Illuminate\Http\Request;

/**
 * Página real do site para a prévia de alertas do painel. Os alertas ativos e o
 * aviso de cookies não aparecem; no lugar deles entra o alerta em edição, enviado
 * pelo editor (postMessage), ou um alerta salvo (?alerta=id, usado na listagem).
 */
class AlertPreviewController extends Controller
{
    public function __invoke(Request $request)
    {
        // Rota fora do Filament: a área precisa ser conferida aqui também
        abort_unless($request->user()?->canAccessArea(AccessArea::Website), 403);

        $slug = trim((string) $request->query('pagina', '/'), '/') ?: '/';

        $page = Page::where('slug', $slug)->visible()->first()
            ?? Page::where('slug', '/')->visible()->first()
            ?? Page::visible()->orderBy('sort_order')->first();

        $alerts = Alert::query()->whereKey((int) $request->query('alerta'))->get();

        $response = $page
            ? response()->view('page', [
                'page' => $page,
                'meta' => [
                    'title' => 'Pré-visualização de alerta',
                    'description' => null,
                    'keywords' => null,
                    'og_image' => null,
                ],
                'alertPreview' => $alerts,
            ])
            // Sem página publicada: o alerta aparece sobre o layout vazio do site
            : response()->view('alert-preview', ['alerts' => $alerts]);

        return $response
            ->header('Cache-Control', 'no-store, private')
            ->header('X-Robots-Tag', 'noindex, nofollow')
            ->header('X-Frame-Options', 'SAMEORIGIN');
    }
}
