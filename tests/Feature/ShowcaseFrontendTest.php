<?php

use App\Livewire\ShowcaseFeed;
use App\Models\Showcase;
use Livewire\Livewire;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('it displays active showcases', function () {
    Showcase::factory()->create(['title' => 'Obra 1', 'is_active' => true]);
    Showcase::factory()->create(['title' => 'Obra 2', 'is_active' => false]);

    Livewire::test(ShowcaseFeed::class)
        ->assertSee('Obra 1')
        ->assertDontSee('Obra 2');
});

test('it paginates results by 2', function () {
    Showcase::factory()->count(3)->create(['is_active' => true]);

    Livewire::test(ShowcaseFeed::class)
        ->assertViewHas('showcases', function ($showcases) {
            return $showcases->count() === 2;
        });
});

test('the showcase page is accessible', function () {
    $this->get(route('showcase'))
        ->assertOk()
        ->assertSeeLivewire(ShowcaseFeed::class);
});
