<?php

declare(strict_types=1);

namespace App\Http\Controllers\Area;

use App\Http\Controllers\Controller;
use App\Models\Area;
use App\Services\AreaService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;

final class AreaDeleteController extends Controller
{
    public function __construct(private readonly AreaService $areaService) {}

    /**
     * Soft delete the specified area.
     */
    public function destroy(Area $area): RedirectResponse
    {
        Gate::authorize('delete', $area);

        $this->areaService->archiveArea($area);

        return redirect()
            ->route('areas.index')
            ->with('success', "Bereich '{$area->name}' wurde erfolgreich gelöscht.");
    }

    /**
     * Restore the specified area from soft delete.
     */
    public function restore(int $areaId): RedirectResponse
    {
        $area = $this->areaService->getArchivedArea($areaId);

        Gate::authorize('restore', $area);

        $this->areaService->restoreArea($area);

        return redirect()
            ->route('areas.show', $area)
            ->with('success', "Bereich '{$area->name}' wurde erfolgreich wiederhergestellt.");
    }

    /**
     * Permanently delete the specified area.
     */
    public function forceDelete(int $areaId): RedirectResponse
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
