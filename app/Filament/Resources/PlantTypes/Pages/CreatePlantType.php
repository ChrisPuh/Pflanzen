<?php

declare(strict_types=1);

namespace App\Filament\Resources\PlantTypes\Pages;

use App\Filament\Resources\PlantTypes\PlantTypeResource;
use Filament\Resources\Pages\CreateRecord;

final class CreatePlantType extends CreateRecord
{
    protected static string $resource = PlantTypeResource::class;
}
