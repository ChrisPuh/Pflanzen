<?php

declare(strict_types=1);

namespace App\Http\Controllers\Area\Actions;

use App\Http\Controllers\Area\AreaController;
use App\Http\Requests\Area\AttachPlantToAreaRequest;
use App\Models\Area;
use Illuminate\Http\RedirectResponse;

final class AttachPlantToAreaController extends AreaController
{
    public function __invoke(AttachPlantToAreaRequest $request, Area $area): RedirectResponse
    {
        // TODO implement Data Transfer Object (DTO::fromRequest) pattern here
        $this->areaService->addPlantsToArea($area, $request->validated());

        return redirect()
            ->route('areas.show', $area)
            ->with('success', 'Pflanzen wurden erfolgreich zur Fläche hinzugefügt.');
    }
}
