<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\Area\AreaTypeEnum;
use App\Models\Area;
use App\Models\Garden;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Area>
 */
final class AreaFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     */
    protected $model = Area::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $type = fake()->randomElement(AreaTypeEnum::cases());

        return [
            'garden_id' => Garden::factory(),
            'name' => $this->generateAreaName($type),
            'type' => $type,
            'description' => fake()->optional(0.7)->text(200),
            'size_sqm' => fake()->optional(0.8)->randomFloat(2, 0.5, 100),
            'coordinates' => fake()->optional(0.6)->passthrough([
                'x' => fake()->randomFloat(2, 0, 100),
                'y' => fake()->randomFloat(2, 0, 100),
            ]),
            'dimensions' => fake()->optional(0.5)->passthrough($this->generateDimensions($type)),
            'color' => fake()->optional(0.3)->hexColor(),
            'metadata' => fake()->optional(0.4)->passthrough($this->generateMetadata($type)),
            'is_active' => fake()->boolean(90),
        ];
    }

    /**
     * Create a planting area (flower bed, vegetable bed, herb bed).
     */
    public function plantingArea(): static
    {
        return $this->state(fn (array $attributes): array => [
            'type' => fake()->randomElement([
                AreaTypeEnum::FlowerBed,
                AreaTypeEnum::VegetableBed,
                AreaTypeEnum::HerbBed,
            ]),
            'size_sqm' => fake()->randomFloat(2, 1, 25),
            'is_active' => true,
        ]);
    }

    /**
     * Create a building area.
     */
    public function building(): static
    {
        return $this->state(fn (array $attributes): array => [
            'type' => fake()->randomElement([
                AreaTypeEnum::House,
                AreaTypeEnum::Greenhouse,
                AreaTypeEnum::Shed,
            ]),
            'dimensions' => [
                'length' => fake()->randomFloat(2, 3, 20),
                'width' => fake()->randomFloat(2, 3, 15),
                'height' => fake()->randomFloat(2, 2.5, 8),
            ],
            'is_active' => true,
        ]);
    }

    /**
     * Create a water feature.
     */
    public function waterFeature(): static
    {
        return $this->state(fn (array $attributes): array => [
            'type' => fake()->randomElement([
                AreaTypeEnum::Pool,
                AreaTypeEnum::Pond,
            ]),
            'dimensions' => [
                'length' => fake()->randomFloat(2, 2, 15),
                'width' => fake()->randomFloat(2, 1, 10),
                'height' => fake()->randomFloat(2, 0.3, 2.5),
            ],
            'metadata' => [
                'water_capacity' => fake()->numberBetween(100, 50000),
                'filtration_system' => fake()->randomElement(['biological', 'mechanical', 'chemical']),
            ],
            'is_active' => true,
        ]);
    }

    /**
     * Create an active area.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes): array => [
            'is_active' => true,
        ]);
    }

    /**
     * Create an inactive area.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes): array => [
            'is_active' => false,
        ]);
    }

    /**
     * Create an area with specific coordinates.
     */
    public function withCoordinates(float $x, float $y): static
    {
        return $this->state(fn (array $attributes): array => [
            'coordinates' => ['x' => $x, 'y' => $y],
        ]);
    }

    /**
     * Create an area with specific color.
     */
    public function withColor(string $color): static
    {
        return $this->state(fn (array $attributes): array => [
            'color' => $color,
        ]);
    }

    /**
     * Generate a realistic area name based on type.
     */
    private function generateAreaName(AreaTypeEnum $type): string
    {
        $names = match ($type) {
            AreaTypeEnum::FlowerBed => [
                'Rosenbeet', 'Staudenbeet', 'Frühlingsbeet', 'Sommerbeet',
                'Hauptbeet', 'Vorgartenbeet', 'Schattenbeet', 'Sonnenbeet',
            ],
            AreaTypeEnum::VegetableBed => [
                'Gemüsebeet 1', 'Hochbeet', 'Hauptgemüsebeet', 'Kleines Gemüsebeet',
                'Tomatenbeet', 'Kräuterbeet', 'Salatbeet', 'Kartoffelbeet',
            ],
            AreaTypeEnum::HerbBed => [
                'Kräutergarten', 'Kräuterspirale', 'Mediterranes Beet', 'Teekräuterbeet',
                'Heilkräuterbeet', 'Küchenkräuter', 'Duftbeet', 'Gewürzbeet',
            ],
            AreaTypeEnum::Lawn => [
                'Hauptrasen', 'Spielrasen', 'Zierrasen', 'Liegewiese',
                'Vorderrasen', 'Hinterrasen', 'Schattenrasen', 'Parkrasen',
            ],
            AreaTypeEnum::Meadow => [
                'Blumenwiese', 'Naturwiese', 'Wildblumenwiese', 'Sommerwiese',
                'Insektenwiese', 'Trockenwiese', 'Bergwiese', 'Frühlingswiese',
            ],
            AreaTypeEnum::Terrace => [
                'Hauptterrasse', 'Südterrasse', 'Holzterrasse', 'Steinterrasse',
                'Esstterrasse', 'Sonnenterrasse', 'Abendterrasse', 'Gartenterrasse',
            ],
            AreaTypeEnum::Patio => [
                'Innenhof', 'Atrium', 'Lichthof', 'Eingangshof',
                'Gartenhof', 'Steinhof', 'Grüner Hof', 'Ruhehof',
            ],
            AreaTypeEnum::House => [
                'Haupthaus', 'Wohnhaus', 'Gartenhaus', 'Ferienhaus',
                'Einfamilienhaus', 'Villa', 'Bungalow', 'Landhaus',
            ],
            AreaTypeEnum::Greenhouse => [
                'Gewächshaus', 'Wintergarten', 'Orangerie', 'Glashaus',
                'Treibhaus', 'Tomatenhaus', 'Anzuchthaus', 'Kalthaus',
            ],
            AreaTypeEnum::Pool => [
                'Schwimmbecken', 'Pool', 'Naturpool', 'Infinity Pool',
                'Aufstellpool', 'Planschbecken', 'Swimmingpool', 'Biopool',
            ],
            AreaTypeEnum::Pond => [
                'Gartenteich', 'Koiteich', 'Naturteich', 'Schwimmteich',
                'Zierteich', 'Bachlauf', 'Wasserspiel', 'Biotop',
            ],
            AreaTypeEnum::Shed => [
                'Geräteschuppen', 'Gartenhaus', 'Werkzeugschuppen', 'Holzschuppen',
                'Aufbewahrung', 'Gartenhütte', 'Materialschuppen', 'Lagerraum',
            ],
            AreaTypeEnum::Compost => [
                'Kompost', 'Kompostplatz', 'Biomüll', 'Komposthaufen',
                'Schnellkomposter', 'Thermokomposter', 'Laubkompost', 'Kompostecke',
            ],
            AreaTypeEnum::Pathway => [
                'Hauptweg', 'Gartenweg', 'Kiesweg', 'Steinweg',
                'Holzweg', 'Natursteinweg', 'Rundweg', 'Zugangsweg',
            ],
            AreaTypeEnum::Rockery => [
                'Steingarten', 'Alpinum', 'Kiesgarten', 'Felsgarten',
                'Trockenmauer', 'Steinbeet', 'Alpenpflanzen', 'Sukkulentenbeet',
            ],
            AreaTypeEnum::TreeArea => [
                'Baumgruppe', 'Gehölz', 'Obstgarten', 'Waldgarten',
                'Baumschule', 'Hecke', 'Strauchgarten', 'Nadelgehölz',
            ],
            AreaTypeEnum::Playground => [
                'Spielplatz', 'Sandkasten', 'Spielbereich', 'Kinderspielplatz',
                'Schaukel', 'Rutsche', 'Klettergerüst', 'Spielwiese',
            ],
            AreaTypeEnum::Seating => [
                'Sitzplatz', 'Gartenbank', 'Pavillon', 'Pergola',
                'Ruheplatz', 'Aussichtspunkt', 'Grillplatz', 'Leseecke',
            ],
            AreaTypeEnum::Storage => [
                'Lager', 'Abstellplatz', 'Materiallager', 'Brennholz',
                'Gartengeräte', 'Aufbewahrung', 'Vorratslager', 'Geräteplatz',
            ],
            AreaTypeEnum::Other => [
                'Sonderbereich', 'Versuchsfläche', 'Projektbereich', 'Testfeld',
                'Experimentierbereich', 'Musterfläche', 'Demogarten', 'Probefläche',
            ],
        };

        return fake()->randomElement($names);
    }

    /**
     * Generate realistic dimensions based on area type.
     */
    private function generateDimensions(AreaTypeEnum $type): array
    {
        return match ($type->category()) {
            'Pflanzbereich' => [
                'length' => fake()->randomFloat(2, 1, 10),
                'width' => fake()->randomFloat(2, 0.5, 5),
            ],
            'Gebäude' => [
                'length' => fake()->randomFloat(2, 3, 20),
                'width' => fake()->randomFloat(2, 3, 15),
                'height' => fake()->randomFloat(2, 2.5, 8),
            ],
            'Wasserelement' => [
                'length' => fake()->randomFloat(2, 2, 15),
                'width' => fake()->randomFloat(2, 1, 10),
                'height' => fake()->randomFloat(2, 0.3, 2.5),
            ],
            default => [
                'length' => fake()->randomFloat(2, 1, 8),
                'width' => fake()->randomFloat(2, 1, 6),
            ],
        };
    }

    /**
     * Generate metadata based on area type.
     */
    private function generateMetadata(AreaTypeEnum $type): array
    {
        $baseMetadata = [
            'maintenance_frequency' => fake()->randomElement(['daily', 'weekly', 'monthly', 'seasonal']),
            'sun_exposure' => fake()->randomElement(['full_sun', 'partial_sun', 'partial_shade', 'full_shade']),
            'soil_type' => fake()->randomElement(['clay', 'sandy', 'loamy', 'rocky', 'organic']),
        ];

        $typeSpecificMetadata = match ($type) {
            AreaTypeEnum::FlowerBed, AreaTypeEnum::VegetableBed, AreaTypeEnum::HerbBed => [
                'irrigation_system' => fake()->optional()->randomElement(['drip', 'sprinkler', 'manual']),
                'mulch_type' => fake()->optional()->randomElement(['bark', 'straw', 'compost', 'stone']),
                'plant_spacing' => fake()->optional()->randomFloat(2, 0.1, 1),
            ],
            AreaTypeEnum::Pool, AreaTypeEnum::Pond => [
                'water_capacity' => fake()->numberBetween(100, 50000),
                'filtration_system' => fake()->optional()->randomElement(['biological', 'mechanical', 'chemical']),
                'heating' => fake()->optional()->boolean(),
            ],
            AreaTypeEnum::House, AreaTypeEnum::Greenhouse, AreaTypeEnum::Shed => [
                'material' => fake()->randomElement(['wood', 'concrete', 'brick', 'metal', 'glass']),
                'year_built' => fake()->optional()->numberBetween(1950, 2024),
                'insulated' => fake()->optional()->boolean(),
            ],
            default => [],
        };

        return array_merge($baseMetadata, $typeSpecificMetadata);
    }
}
