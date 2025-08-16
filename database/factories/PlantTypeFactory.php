<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\PlantTypeEnum;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PlantType>
 */
final class PlantTypeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->randomElement(PlantTypeEnum::cases()),
            'description' => fake()->optional(0.8)->paragraph(),
        ];
    }
}
