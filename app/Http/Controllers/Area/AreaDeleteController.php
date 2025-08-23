<?php

declare(strict_types=1);

namespace App\Http\Controllers\Area;

use App\DTOs\Area\AreaDeleteDTO;
use App\Http\Requests\Area\AreaDeleteRequest;
use App\Models\Area;
use Illuminate\Http\RedirectResponse;

final class AreaDeleteController extends AreaController
{
    /**
     * Soft delete the specified area.
     */
    public function __invoke(AreaDeleteRequest $request, Area $area): RedirectResponse
    {
        $this->areaService->deleteArea($area, AreaDeleteDTO::fromValidatedRequest($request->validated()));

        return redirect()
            ->route('areas.index')
            ->with('success', "Bereich '{$area->name}' wurde erfolgreich gelöscht.");
    }
}
