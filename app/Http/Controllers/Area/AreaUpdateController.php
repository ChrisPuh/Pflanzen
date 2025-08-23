<?php

declare(strict_types=1);

namespace App\Http\Controllers\Area;

use App\DTOs\Area\AreaUpdateDTO;
use App\Http\Requests\Area\AreaUpdateRequest;
use App\Models\Area;
use Illuminate\Http\RedirectResponse;
use Throwable;

final class AreaUpdateController extends AreaController
{
    /**
     * TODO handle potential exceptions from the service layer
     *
     * @throws Throwable
     */
    public function __invoke(AreaUpdateRequest $request, Area $area): RedirectResponse
    {
        // TODO implement Data Transfer Object (DTO) pattern for request data
        $area = $this->areaService->updateArea($area, AreaUpdateDTO::fromValidated($request->validated()));

        return redirect()
            ->route('areas.show', $area)
            ->with('success', "Bereich '$area->name' wurde erfolgreich aktualisiert.");
    }
}
