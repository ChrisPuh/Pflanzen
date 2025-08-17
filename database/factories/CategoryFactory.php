<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\PlantCategoryEnum;
use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Category>
 */
final class CategoryFactory extends Factory
{
    protected $model = Category::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->randomElement(PlantCategoryEnum::cases()),
            'description' => fake()->optional(0.8)->paragraph(),
        ];
    }
}
