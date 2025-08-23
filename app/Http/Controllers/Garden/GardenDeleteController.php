<?php

declare(strict_types=1);

namespace App\Http\Controllers\Garden;

use App\Http\Controllers\Controller;
use App\Models\Garden;
use App\Services\GardenService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;

final class GardenDeleteController extends Controller
{
    public function __construct(
        private readonly GardenService $gardenService
    ) {}

    /**
     * Soft delete (archive) the specified garden.
     */
    public function destroy(Garden $garden): RedirectResponse
    {
        Gate::authorize('delete', $garden);

        $gardenName = $garden->name;

        $this->gardenService->archiveGarden($garden);

        return redirect()
            ->route('gardens.index')
            ->with('status', "Garten \"{$gardenName}\" wurde archiviert.");
    }

    /**
     * Restore the specified garden from archive.
     */
    public function restore(int $gardenId): RedirectResponse
    {
        $garden = $this->gardenService->getArchivedGardenById($gardenId);

        Gate::authorize('restore', $garden);

        $this->gardenService->restoreGarden($garden);

        return redirect()
            ->route('gardens.show', $garden)
            ->with('status', "Garten \"{$garden->name}\" wurde wiederhergestellt.");
    }
}
