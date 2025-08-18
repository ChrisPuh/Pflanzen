<?php

declare(strict_types=1);

namespace App\Http\Controllers\Garden;

use App\Http\Controllers\Controller;
use App\Models\Garden;
use App\Services\GardenService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

final class GardenShowController extends Controller
{
    public function __construct(
        private readonly GardenService $gardenService
    ) {}

    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request, Garden $garden): View
    {
        Gate::authorize('view', $garden);

        $garden = $this->gardenService->getGardenForDisplay($garden);

        return view('gardens.show', [
            'garden' => $garden,
        ]);
    }
}
