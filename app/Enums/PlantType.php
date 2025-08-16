<?php

declare(strict_types=1);

namespace App\Enums;

use App\Traits\HasValues;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;

enum PlantType: string implements FilamentUser
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

    public function label(): string
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

    public function canAccessPanel(Panel $panel): bool
    {
        // TODO Adjust this logic based on your access control needs
        // maybe check user roles or permissions
        // schould be a policy that we can do something like this:
        // return auth()->user()->can('access', $panel);
        return true;
    }
}
