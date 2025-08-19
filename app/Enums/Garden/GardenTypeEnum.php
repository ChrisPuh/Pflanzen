<?php

declare(strict_types=1);

namespace App\Enums\Garden;

use App\Traits\HasValues;
use Illuminate\Support\Collection;

enum GardenTypeEnum: string
{
    use HasValues;
    case VegetableGarden = 'vegetable_garden';
    case FlowerGarden = 'flower_garden';
    case HerbGarden = 'herb_garden';
    case FruitGarden = 'fruit_garden';
    case MixedGarden = 'mixed_garden';
    case GreenhouseGarden = 'greenhouse_garden';
    case ContainerGarden = 'container_garden';
    case RooftopGarden = 'rooftop_garden';
    case BalconyGarden = 'balcony_garden';
    case IndoorGarden = 'indoor_garden';

    public static function options(): Collection
    {
        return collect(self::cases())->map(fn (self $case): array => [
            'value' => $case->value,
            'label' => $case->getLabel(),
            'description' => $case->description(),
        ]);
    }

    public function getLabel(): string
    {
        return match ($this) {
            self::VegetableGarden => 'Nutzgarten',
            self::FlowerGarden => 'Blumengarten',
            self::HerbGarden => 'Kräutergarten',
            self::FruitGarden => 'Obstgarten',
            self::MixedGarden => 'Mischgarten',
            self::GreenhouseGarden => 'Gewächshausgarten',
            self::ContainerGarden => 'Containergarten',
            self::RooftopGarden => 'Dachgarten',
            self::BalconyGarden => 'Balkongarten',
            self::IndoorGarden => 'Zimmergarten',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::VegetableGarden => 'Ein Garten zum Anbau von Gemüse und anderen essbaren Pflanzen',
            self::FlowerGarden => 'Ein Garten mit Fokus auf Zierpflanzen und Blumen',
            self::HerbGarden => 'Ein spezieller Garten für Kräuter und Gewürze',
            self::FruitGarden => 'Ein Garten mit Obstbäumen und Beerensträuchern',
            self::MixedGarden => 'Ein vielseitiger Garten mit verschiedenen Pflanzenarten',
            self::GreenhouseGarden => 'Ein geschützter Garten in einem Gewächshaus',
            self::ContainerGarden => 'Ein Garten in Töpfen und Containern',
            self::RooftopGarden => 'Ein Garten auf dem Dach',
            self::BalconyGarden => 'Ein kleiner Garten auf dem Balkon',
            self::IndoorGarden => 'Ein Garten im Innenbereich',
        };
    }
}
