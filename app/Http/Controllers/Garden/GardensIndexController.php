<?php

declare(strict_types=1);

namespace App\Http\Controllers\Garden;

use App\Http\Controllers\Controller;
use App\Models\Garden;
use App\Services\GardenService;
use App\Traits\AuthenticatedUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

final class GardensIndexController extends Controller
{
    use AuthenticatedUser;

    public function __construct(
        private readonly GardenService $gardenService
    ) {}

    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request): View
    {
        Gate::authorize('viewAny', Garden::class);

        ['user' => $user, 'isAdmin' => $isAdmin] = $this->getUserAndAdminStatus();

        // Get all data from service in one call
        $indexData = $this->gardenService->getGardensIndexData(
            user: $user,
            isAdmin: $isAdmin,
            perPage: 12
        );

        $indexData['isAdmin'] = $isAdmin;

        return view('gardens.index', $indexData);
    }

    /**
     * Show archived gardens for the authenticated user.
     */
    public function archived(Request $request): View
    {
        Gate::authorize('viewAny', Garden::class);

        ['user' => $user, 'isAdmin' => $isAdmin] = $this->getUserAndAdminStatus();

        $archivedData = $this->gardenService->getArchivedData($user, $isAdmin);

        return view('gardens.archived', $archivedData);
    }
}
