<?php

namespace Database\Factories;

use App\Models\Page;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Page>
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
        $slug = str($title)->slug();

        return [
            'title' => $title,
            'slug' => $slug,
            'content' => [],
            'is_active_in_menu' => true,
            'is_visible' => true,
            'sort_order' => 0,
        ];
    }

    /**
     * Página inicial (index) com slug raiz.
     */
    public function index(): static
    {
        return $this->state(fn (array $attributes) => [
            'title' => 'Página Inicial',
            'slug' => '/',
            'is_active_in_menu' => true,
            'is_visible' => true,
        ]);
    }
}
