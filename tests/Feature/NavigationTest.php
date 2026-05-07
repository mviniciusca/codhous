<?php

use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    // Criar as configurações iniciais para evitar erro de tabela vazia
    Setting::create([
        'settings' => [
            'maintenance_mode' => false,
            'discovery_mode' => false,
        ]
    ]);

    \App\Models\Page::create([
        'title' => 'Home',
        'slug' => '/',
        'is_visible' => true,
        'content' => [],
    ]);

    \App\Models\Page::create([
        'title' => 'Contato',
        'slug' => 'contato',
        'is_visible' => true,
        'content' => [],
    ]);

    \App\Models\Page::create([
        'title' => 'Sobre',
        'slug' => 'sobre',
        'is_visible' => true,
        'content' => [],
    ]);
});

test('página inicial está acessível', function () {
    $response = $this->get('/');
    $response->assertStatus(200);
});

test('página de contato está acessível', function () {
    $response = $this->get('/contato'); // Ajuste o slug se necessário
    $response->assertStatus(200);
});

test('página sobre está acessível', function () {
    $response = $this->get('/sobre'); // Ajuste o slug se necessário
    $response->assertStatus(200);
});
