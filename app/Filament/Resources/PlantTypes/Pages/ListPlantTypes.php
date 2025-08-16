<?php

declare(strict_types=1);

namespace App\Filament\Resources\PlantTypes\Pages;

use App\Filament\Resources\PlantTypes\PlantTypeResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

final class ListPlantTypes extends ListRecords
{
    protected static string $resource = PlantTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
