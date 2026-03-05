<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Z3d0X\FilamentFabricator\Models\Page;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\Z3d0X\FilamentFabricator\Models\Page>
 */
class PageFactory extends Factory
{
    protected $model = Page::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = fake()->unique()->words(3, true);
        $slug = '/' . str($title)->slug();

        return [
            'title' => $title,
            'slug' => $slug,
            'layout' => 'default',
            'blocks' => [],
            'parent_id' => null,
        ];
    }

    /**
     * Página inicial (index) com slug raiz.
     */
    public function index(): static
    {
        return $this->state(fn (array $attributes) => [
            'title' => 'index',
            'slug' => '/',
            'layout' => 'default',
            'blocks' => [],
            'parent_id' => null,
        ]);
    }
}
