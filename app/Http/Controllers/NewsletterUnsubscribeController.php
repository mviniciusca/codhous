<?php

namespace App\Http\Controllers;

use App\Models\Newsletter;
use Illuminate\Http\Request;

/**
 * Cancelamento de inscrição pelo link do e-mail (URL assinada, sem expiração).
 * O GET só mostra a confirmação, para que antivírus e pré-visualizadores de link não
 * descadastrem ninguém; o POST desativa e também atende o "cancelar inscrição" de um clique
 * dos clientes de e-mail (cabeçalho List-Unsubscribe-Post).
 */
class NewsletterUnsubscribeController extends Controller
{
    public function show(Request $request, Newsletter $newsletter)
    {
        return view('newsletter.unsubscribe', [
            'subscriber' => $newsletter,
            'done' => ! $newsletter->is_active,
            'action' => $request->fullUrl(),
        ]);
    }

    public function store(Newsletter $newsletter)
    {
        if ($newsletter->is_active) {
            $newsletter->forceFill(['is_active' => false])->save();
        }

        return view('newsletter.unsubscribe', [
            'subscriber' => $newsletter,
            'done' => true,
            'action' => null,
        ]);
    }
}
