<?php

declare(strict_types=1);

namespace App\Http\Controllers\Area;

use App\Http\Controllers\Controller;
use App\Http\Requests\Area\AreaUpdateRequest;
use App\Models\Area;
use Illuminate\Http\RedirectResponse;

final class AreaUpdateController extends Controller
{
    public function __construct(private readonly \App\Services\AreaService $areaService) {}

    public function __invoke(AreaUpdateRequest $request, Area $area): RedirectResponse
    {
        // TODO implement Data Transfer Object (DTO) pattern for request data
        $area = $this->areaService->updateArea($area, $request->validated());

        return redirect()
            ->route('areas.show', $area)
            ->with('success', "Bereich '{$area->name}' wurde erfolgreich aktualisiert.");
    }
}
