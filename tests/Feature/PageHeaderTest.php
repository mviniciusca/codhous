<?php

use App\Filament\Resources\ContentSectionResource\Pages\CreateContentSection;
use App\Filament\Resources\ContentSectionResource\Pages\ListContentSections;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Livewire\livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    Setting::create(['settings' => ['maintenance_mode' => false]]);

    $this->actingAs(User::factory()->create());
});

test('cabeçalho compacto é usado nas páginas do painel', function () {
    $this->get('/admin/content-sections')
        ->assertOk()
        ->assertSee('cx-pagebar', escape: false)
        ->assertDontSee('fi-header-heading text-2xl', escape: false);
});

test('listagem não repete na trilha o link da própria página', function () {
    livewire(ListContentSections::class)
        ->assertOk()
        ->assertDontSee('class="cx-pagebar-crumb"', escape: false);
});

test('subpágina mostra a trilha até a listagem e o subtítulo como dica', function () {
    livewire(CreateContentSection::class)
        ->assertOk()
        ->assertSeeHtml('class="cx-pagebar-crumb">Seções</a>')
        ->assertSee('cx-pagebar-hint', escape: false)
        ->assertSee('Escolha um componente, configure o conteúdo e ajuste a aparência.');
});
