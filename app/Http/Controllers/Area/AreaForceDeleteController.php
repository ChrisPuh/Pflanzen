<?php

declare(strict_types=1);

namespace App\Http\Controllers\Area;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;

final class AreaForceDeleteController extends Controller
{
    // TODO implement AreaController and move common logic there
    public function __construct(private readonly \App\Services\Area\AreaService $areaService) {}

    /**
     * Permanently delete the specified area.
     */
    public function __invoke(int $areaId): RedirectResponse
    {
        $area = $this->areaService->getArchivedArea($areaId);

        Gate::authorize('forceDelete', $area);

        $areaName = $area->name;

        $this->areaService->forceDeleteArea($area);

        return redirect()
            ->route('areas.index')
            ->with('success', "Bereich '{$areaName}' wurde permanent gelöscht.");
    }
}
