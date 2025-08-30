<?php

declare(strict_types=1);

namespace App\Http\Controllers\Area\Actions;

use App\DTOs\Area\Actions\DetachPlantFromAreaDTO;
use App\Http\Controllers\Area\AreaController;
use App\Models\Area;
use App\Models\Plant;
use App\Services\Area\AreaService;
use App\Services\Area\Contracts\AreaPlantServiceInterface;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;

final class DetachPlantFromAreaController extends AreaController
{
    public function __construct(
        AreaService $areaService,
        private readonly AreaPlantServiceInterface $areaPlantService
    ) {
        parent::__construct($areaService);
    }

    public function __invoke(Area $area, Plant $plant): RedirectResponse
    {
        Gate::authorize('update', $area);

        $dto = DetachPlantFromAreaDTO::fromIds($plant->id);
        $this->areaPlantService->detachPlantFromArea($area, $dto);

        return redirect()
            ->route('areas.show', $area)
            ->with('success', 'Pflanze wurde erfolgreich von der Fläche entfernt.');
    }
}
