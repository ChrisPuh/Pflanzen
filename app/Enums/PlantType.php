<?php

declare(strict_types=1);

namespace App\Enums;

use App\Traits\HasValues;
use Filament\Support\Contracts\HasLabel;
use Illuminate\Contracts\Support\Htmlable;

enum PlantType: string implements HasLabel
{
    use HasValues;

    case Herb = 'herb';
    case Flower = 'flower';
    case Tree = 'tree';
    case Shrub = 'shrub';
    case Succulent = 'succulent';
    case Fern = 'fern';
    case Grass = 'grass';
    case Vegetable = 'vegetable';
    case Fruit = 'fruit';
    case Aquatic = 'aquatic';

    public function getLabel(): string|Htmlable|null
    {
        return match ($this) {
            self::Herb => 'Kräuter',
            self::Flower => 'Blumen',
            self::Tree => 'Bäume',
            self::Shrub => 'Sträucher',
            self::Succulent => 'Sukkulenten',
            self::Fern => 'Farne',
            self::Grass => 'Gräser',
            self::Vegetable => 'Gemüse',
            self::Fruit => 'Obst',
            self::Aquatic => 'Wasserpflanzen',
        };
    }
}
