<?php

declare(strict_types=1);

namespace App\Http\Controllers\Garden;

use App\Http\Controllers\Controller;
use App\Http\Requests\Garden\GardenCreateRequest;
use App\Models\Garden;
use App\Services\GardenService;
use App\Traits\AuthenticatedUser;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

final class GardenCreateController extends Controller
{
    use AuthenticatedUser;

    public function __construct(
        private readonly GardenService $gardenService
    ) {}

    /**
     * Show the form for creating a new garden.
     */
    public function create(): View
    {
        Gate::authorize('create', Garden::class);

        $createData = $this->gardenService->getCreateData();

        return view('gardens.create', $createData);
    }

    /**
     * Store a newly created garden in storage.
     */
    public function store(GardenCreateRequest $request): RedirectResponse
    {
        // Authorization handled by GardenCreateRequest::authorize()

        $user = $this->getUser();

        // TODO implement Data Transfer Object (DTO) pattern here
        $garden = $this->gardenService->createGarden(user: $user, data: $request->validated());

        return redirect()
            ->route('gardens.show', $garden)
            ->with('success', 'Garten wurde erfolgreich erstellt!');
    }
}
