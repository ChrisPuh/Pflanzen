<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\Garden\GardenTypeEnum;
use App\Models\Garden;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Garden>
 */
final class GardenFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $gardenNames = [
            'Mein Gemüsegarten',
            'Kräuterparadies',
            'Blütenpracht',
            'Obstgarten',
            'Balkongrün',
            'Gewächshaus Eden',
            'Stadtgarten',
            'Terrassengarten',
            'Wildblumenwiese',
            'Zen-Garten',
        ];

        $locations = [
            'Hinterhof',
            'Vorgarten',
            'Balkon',
            'Terrasse',
            'Dachgarten',
            'Gewächshaus',
            'Schrebergarten',
            'Gemeinschaftsgarten',
        ];

        $cities = [
            'Berlin',
            'München',
            'Hamburg',
            'Köln',
            'Frankfurt',
            'Stuttgart',
            'Düsseldorf',
            'Leipzig',
            'Dresden',
            'Hannover',
        ];

        $type = fake()->randomElement(GardenTypeEnum::cases());
        $hasCoordinates = fake()->boolean(70);

        return [
            'user_id' => User::factory(),
            'name' => fake()->randomElement($gardenNames),
            'type' => $type,
            'location' => fake()->optional(0.8)->randomElement($locations),
            'size_sqm' => fake()->optional(0.7)->randomFloat(2, 1, 500),
            'description' => fake()->optional(0.6)->realText(fake()->numberBetween(50, 200)),
            'coordinates' => $hasCoordinates ? [
                'lat' => fake()->latitude(47.0, 55.0), // Deutschland Breitengrad
                'lng' => fake()->longitude(5.0, 15.0),  // Deutschland Längengrad
            ] : null,
            'postal_code' => fake()->optional(0.8)->postcode(),
            'city' => fake()->optional(0.8)->randomElement($cities),
            'country' => 'DE',
            'is_active' => fake()->boolean(90),
            'established_at' => fake()->optional(0.7)->dateTimeBetween('-10 years', 'now'),
        ];
    }

    public function vegetableGarden(): self
    {
        return $this->state(fn (array $attributes): array => [
            'type' => GardenTypeEnum::VegetableGarden,
            'name' => 'Mein Gemüsegarten',
            'size_sqm' => fake()->randomFloat(2, 10, 200),
        ]);
    }

    public function flowerGarden(): self
    {
        return $this->state(fn (array $attributes): array => [
            'type' => GardenTypeEnum::FlowerGarden,
            'name' => 'Blumengarten',
            'size_sqm' => fake()->randomFloat(2, 5, 100),
        ]);
    }

    public function herbGarden(): self
    {
        return $this->state(fn (array $attributes): array => [
            'type' => GardenTypeEnum::HerbGarden,
            'name' => 'Kräutergarten',
            'size_sqm' => fake()->randomFloat(2, 2, 50),
        ]);
    }

    public function balconyGarden(): self
    {
        return $this->state(fn (array $attributes): array => [
            'type' => GardenTypeEnum::BalconyGarden,
            'name' => 'Balkongarten',
            'location' => 'Balkon',
            'size_sqm' => fake()->randomFloat(2, 1, 15),
        ]);
    }

    public function withCoordinates(?float $lat = null, ?float $lng = null): self
    {
        return $this->state(fn (array $attributes): array => [
            'coordinates' => [
                'lat' => $lat ?? fake()->latitude(47.0, 55.0),
                'lng' => $lng ?? fake()->longitude(5.0, 15.0),
            ],
        ]);
    }

    public function withoutCoordinates(): self
    {
        return $this->state(fn (array $attributes): array => [
            'coordinates' => null,
        ]);
    }

    public function inactive(): self
    {
        return $this->state(fn (array $attributes): array => [
            'is_active' => false,
        ]);
    }

    public function large(): self
    {
        return $this->state(fn (array $attributes): array => [
            'size_sqm' => fake()->randomFloat(2, 200, 1000),
        ]);
    }

    public function small(): self
    {
        return $this->state(fn (array $attributes): array => [
            'size_sqm' => fake()->randomFloat(2, 1, 10),
        ]);
    }

    public function established(string $date): self
    {
        return $this->state(fn (array $attributes): array => [
            'established_at' => $date,
        ]);
    }
}
