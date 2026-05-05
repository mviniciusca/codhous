<?php

use App\Models\Showcase;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('it can create a showcase entry', function () {
    $showcase = Showcase::create([
        'title' => 'Obra em São Paulo',
        'description' => 'Descrição da obra',
        'location' => 'São Paulo, SP',
        'images' => ['image1.jpg', 'image2.jpg'],
        'is_active' => true,
    ]);

    expect($showcase->title)->toBe('Obra em São Paulo');
    expect($showcase->description)->toBe('Descrição da obra');
    expect($showcase->location)->toBe('São Paulo, SP');
    expect($showcase->images)->toBe(['image1.jpg', 'image2.jpg']);
    expect($showcase->is_active)->toBeTrue();
});

test('it can scope active showcases', function () {
    Showcase::factory()->create(['is_active' => true]);
    Showcase::factory()->create(['is_active' => false]);

    expect(Showcase::active()->count())->toBe(1);
});
