<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('versão do app vem do version.txt em formato SemVer', function () {
    expect(config('app.version'))
        ->toBe(trim(file_get_contents(base_path('version.txt'))))
        ->toMatch('/^\d+\.\d+\.\d+$/');
});

test('versão do app aparece no menu do usuário do painel', function () {
    $this->actingAs(User::factory()->create())
        ->get('/admin')
        ->assertOk()
        ->assertSee('Codhous v'.config('app.version'));
});
