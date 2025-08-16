<?php

declare(strict_types=1);

namespace App\Filament\Resources\PlantCategories\Pages;

use App\Filament\Resources\PlantCategories\PlantCategoryResource;
use Filament\Resources\Pages\CreateRecord;

final class CreatePlantCategory extends CreateRecord
{
    protected static string $resource = PlantCategoryResource::class;
}
