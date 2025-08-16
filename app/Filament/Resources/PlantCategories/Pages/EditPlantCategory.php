<?php

declare(strict_types=1);

namespace App\Filament\Resources\PlantCategories\Pages;

use App\Filament\Resources\PlantCategories\PlantCategoryResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

final class EditPlantCategory extends EditRecord
{
    protected static string $resource = PlantCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
