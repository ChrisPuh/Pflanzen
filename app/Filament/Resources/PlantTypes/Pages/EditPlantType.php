<?php

declare(strict_types=1);

namespace App\Filament\Resources\PlantTypes\Pages;

use App\Filament\Resources\PlantTypes\PlantTypeResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

final class EditPlantType extends EditRecord
{
    protected static string $resource = PlantTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
