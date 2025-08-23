<?php

declare(strict_types=1);

namespace App\Http\Controllers\Area;

use App\Http\Controllers\Controller;
use App\Http\Requests\Area\AreaPlantRequest;
use App\Models\Area;
use App\Models\Plant;
use App\Services\AreaService;
use App\Traits\AuthenticatedUser;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;

final class AreaPlantController extends Controller
{
    use AuthenticatedUser;

    public function __construct(private readonly AreaService $areaService) {}

    public function store(AreaPlantRequest $request, Area $area): RedirectResponse
    {
        Gate::authorize('update', $area);

        // TODO implement Data Transfer Object (DTO::fromRequest) pattern here
        $this->areaService->addPlantsToArea($area, $request->validated());

        return redirect()
            ->route('areas.show', $area)
            ->with('success', 'Pflanzen wurden erfolgreich zur Fläche hinzugefügt.');
    }

    public function destroy(Area $area, Plant $plant): RedirectResponse
    {
        Gate::authorize('update', $area);

        $this->areaService->removePlantFromArea($area, $plant);

        return redirect()
            ->route('areas.show', $area)
            ->with('success', 'Pflanze wurde erfolgreich von der Fläche entfernt.');
    }
}
