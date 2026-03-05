<?php

namespace Database\Factories;

use App\Models\ContentSection;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ContentSection>
 */
class ContentSectionFactory extends Factory
{
    protected $model = \App\Models\ContentSection::class;

    public function definition(): array
    {
        return [
            'slug' => fake()->unique()->slug(),
            'name' => fake()->words(3, true),
            'type' => ContentSection::TYPE_FAQ,
            'content' => [],
            'is_active' => true,
            'sort_order' => 0,
        ];
    }

    public function forSlug(string $slug, string $type, array $content = []): static
    {
        return $this->state(fn () => [
            'slug' => $slug,
            'name' => ContentSection::typeLabels()[$type] ?? $type,
            'type' => $type,
            'content' => $content,
            'is_active' => true,
        ]);
    }
}
