<?php

declare(strict_types=1);

namespace App\Http\Controllers\Area;

use App\Http\Controllers\Controller;
use App\Models\Area;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;

final class AreaDeleteController extends Controller
{
    /**
     * Soft delete the specified area.
     */
    public function destroy(Area $area): RedirectResponse
    {
        Gate::authorize('delete', $area);

        // Store area name for success message
        $areaName = $area->name;

        // Soft delete the area
        $area->delete();

        return redirect()
            ->route('areas.index')
            ->with('success', "Bereich '{$areaName}' wurde erfolgreich gelöscht.");
    }

    /**
     * Restore the specified area from soft delete.
     */
    public function restore(int $areaId): RedirectResponse
    {
        $area = Area::withTrashed()->findOrFail($areaId);

        Gate::authorize('restore', $area);

        $area->restore();

        return redirect()
            ->route('areas.show', $area)
            ->with('success', "Bereich '{$area->name}' wurde erfolgreich wiederhergestellt.");
    }

    /**
     * Permanently delete the specified area.
     */
    public function forceDelete(int $areaId): RedirectResponse
    {
        $area = Area::withTrashed()->findOrFail($areaId);

        Gate::authorize('forceDelete', $area);

        $areaName = $area->name;

        // Permanently delete the area
        $area->forceDelete();

        return redirect()
            ->route('areas.index')
            ->with('success', "Bereich '{$areaName}' wurde permanent gelöscht.");
    }
}
