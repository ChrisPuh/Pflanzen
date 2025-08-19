<?php

declare(strict_types=1);

namespace App\Http\Controllers\Garden;

use App\Http\Controllers\Controller;
use App\Models\Garden;
use App\Models\User;
use App\Services\GardenService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

final class GardensIndexController extends Controller
{
    public function __construct(
        private readonly GardenService $gardenService
    ) {}

    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request): View
    {
        Gate::authorize('viewAny', Garden::class);

        /** @var User $user */
        $user = $request->user();
        $isAdmin = $user->hasRole('admin');

        $gardens = $this->gardenService->getGardensForUser(
            user: $user,
            isAdmin: $isAdmin,
            perPage: 12
        );

        $hasArchivedGardens = $this->gardenService->getArchivedGardensForUser(
            user: $user,
            isAdmin: $isAdmin
        )->isNotEmpty();

        return view('gardens.index', [
            'gardens' => $gardens,
            'isAdmin' => $isAdmin,
            'hasArchivedGardens' => $hasArchivedGardens,
        ]);
    }

    /**
     * Show archived gardens for the authenticated user.
     */
    public function archived(Request $request): View
    {
        Gate::authorize('viewAny', Garden::class);

        /** @var User $user */
        $user = $request->user();
        $isAdmin = $user->hasRole('admin');

        $archivedGardens = $this->gardenService->getArchivedGardensForUser(
            user: $user,
            isAdmin: $isAdmin
        );

        return view('gardens.archived', [
            'gardens' => $archivedGardens,
            'isAdmin' => $isAdmin,
        ]);
    }
}
