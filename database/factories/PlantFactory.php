<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\PlantCategory;
use App\Models\PlantType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Plant>
 */
final class PlantFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->words(2, true),
            'latin_name' => fake()->optional(0.8)->words(2, true),
            'description' => fake()->optional(0.9)->paragraph(),
            'plant_type_id' => PlantType::factory(),
        ];
    }

    public function withCategories(array $categoryEnums = []): static
    {
        return $this->afterCreating(function ($plant) use ($categoryEnums): void {
            if ($categoryEnums === []) {
                // If no specific categories provided, create random ones
                $categoryEnums = fake()->randomElements(
                    \App\Enums\PlantCategory::cases(),
                    fake()->numberBetween(1, 3)
                );
            }

            $categoryIds = [];
            foreach ($categoryEnums as $categoryEnum) {
                $category = PlantCategory::firstOrCreate([
                    'name' => $categoryEnum,
                ], [
                    'description' => fake()->optional(0.8)->paragraph(),
                ]);
                $categoryIds[] = $category->id;
            }

            $plant->plantCategories()->attach($categoryIds);
        });
    }
}
