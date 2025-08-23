<?php

declare(strict_types=1);

namespace App\Http\Controllers\Area;

use App\Http\Controllers\Controller;
use App\Services\AreaService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;

final class AreaRestoreController extends Controller
{
    public function __construct(private readonly AreaService $areaService) {}

    /**
     * Restore the specified area from soft delete.
     */
    public function __invoke(int $areaId): RedirectResponse
    {
        $area = $this->areaService->getArchivedArea($areaId);

        Gate::authorize('restore', $area);

        $this->areaService->restoreArea($area);

        return redirect()
            ->route('areas.show', $area)
            ->with('success', "Bereich '{$area->name}' wurde erfolgreich wiederhergestellt.");
    }
}
