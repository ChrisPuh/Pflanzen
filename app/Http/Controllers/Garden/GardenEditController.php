<?php

declare(strict_types=1);

namespace App\Http\Controllers\Garden;

use App\Http\Controllers\Controller;
use App\Http\Requests\Garden\GardenEditRequest;
use App\Models\Garden;
use App\Models\User;
use App\Services\GardenService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

final class GardenEditController extends Controller
{
    public function __construct(
        private readonly GardenService $gardenService
    ) {}

    /**
     * Show the form for editing the specified garden.
     */
    public function edit(Garden $garden): View
    {
        Gate::authorize('update', $garden);

        $gardenTypes = $this->gardenService->getAvailableGardenTypes();

        return view('gardens.edit', [
            'garden' => $garden,
            'gardenTypes' => $gardenTypes,
        ]);
    }

    /**
     * Update the specified garden in storage.
     */
    public function update(GardenEditRequest $request, Garden $garden): RedirectResponse
    {
        // Authorization handled by GardenEditRequest::authorize()

        /** @var User $user */
        $request->user();

        $garden = $this->gardenService->updateGarden(
            garden: $garden,
            data: $request->validated()
        );

        return redirect()
            ->route('gardens.show', $garden)
            ->with('status', 'Garten wurde erfolgreich aktualisiert!');
    }
}
