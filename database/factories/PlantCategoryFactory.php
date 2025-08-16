<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\PlantCategory as PlantCategoryEnum;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PlantCategory>
 */
final class PlantCategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->randomElement(PlantCategoryEnum::cases()),
            'description' => fake()->optional(0.8)->paragraph(),
        ];
    }
}
