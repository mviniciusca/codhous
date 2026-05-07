<?php

namespace App\Http\Controllers;

use App\Models\Page;
use Illuminate\Http\Request;

class PageController extends Controller
{
    public function show(string $slug = '/')
    {
        // Se o slug for vazio ou apenas /, buscamos a página inicial
        $slug = $slug === '/' ? '/' : $slug;

        $page = Page::where('slug', $slug)
            ->visible()
            ->firstOrFail();

        return view('page', [
            'page' => $page,
            'meta' => [
                'title' => $page->title,
                'description' => data_get($page->meta, 'description'),
            ]
        ]);
    }
}
