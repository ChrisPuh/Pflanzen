<?php

namespace App\Http\Controllers\Area\Actions;

use App\Http\Controllers\Area\AreaController;
use App\Models\Area;
use App\Models\Plant;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;

class DetachPlantFromAreaController extends AreaController
{
    public function __invoke(Area $area, Plant $plant): RedirectResponse
    {
        Gate::authorize('update', $area);

        $this->areaService->removePlantFromArea($area, $plant);

        return redirect()
            ->route('areas.show', $area)
            ->with('success', 'Pflanze wurde erfolgreich von der Fläche entfernt.');
    }
}
