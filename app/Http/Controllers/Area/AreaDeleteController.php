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
    public function __invoke(Area $area): RedirectResponse
    {
        Gate::authorize('delete', $area);

        $this->areaService->archiveArea($area);

        return redirect()
            ->route('areas.index')
            ->with('success', "Bereich '{$area->name}' wurde erfolgreich gelöscht.");
    }
}
