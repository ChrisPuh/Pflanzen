<?php

declare(strict_types=1);

namespace App\Enums;

use App\Traits\HasValues;

enum PlantCategory: string
{
    use HasValues;

    case Indoor = 'indoor';
    case Outdoor = 'outdoor';
    case Medicinal = 'medicinal';
    case Ornamental = 'ornamental';
    case Edible = 'edible';
    case Aromatic = 'aromatic';
    case Toxic = 'toxic';
    case Native = 'native';
    case Exotic = 'exotic';
    case Rare = 'rare';

    public function label(): string
    {
        return match ($this) {
            self::Indoor => 'Zimmerpflanzen',
            self::Outdoor => 'Gartenpflanzen',
            self::Medicinal => 'Heilpflanzen',
            self::Ornamental => 'Zierpflanzen',
            self::Edible => 'Essbare Pflanzen',
            self::Aromatic => 'Duftpflanzen',
            self::Toxic => 'Giftige Pflanzen',
            self::Native => 'Einheimische Pflanzen',
            self::Exotic => 'Exotische Pflanzen',
            self::Rare => 'Seltene Pflanzen',
        };
    }
}
