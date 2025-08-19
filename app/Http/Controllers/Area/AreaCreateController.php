<?php

declare(strict_types=1);

namespace App\Http\Controllers\Area;

use App\Enums\Area\AreaTypeEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\Area\StoreAreaRequest;
use App\Models\Area;
use App\Models\Garden;
use App\Traits\AuthenticatedUser;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

final class AreaCreateController extends Controller
{
    use AuthenticatedUser;

    public function create(Request $request): View
    {
        ['user' => $user, 'isAdmin' => $isAdmin] = $this->getUserAndAdminStatus();

        // Get user's gardens for dropdown
        $userGardens = Garden::query()
            ->when(! $isAdmin, function (\Illuminate\Database\Eloquent\Builder $query) use ($user): void {
                $query->where('user_id', $user->id);
            })
            ->select('id', 'name', 'type')
            ->orderBy('name')
            ->get();

        // Check if a garden is pre-selected (from URL parameter)
        $selectedGarden = null;
        if ($request->filled('garden_id')) {
            $selectedGarden = $userGardens->firstWhere('id', $request->integer('garden_id'));
        }

        return view('areas.create', [
            'userGardens' => $userGardens,
            'selectedGarden' => $selectedGarden,
            'areaTypes' => AreaTypeEnum::options(),
            'isAdmin' => $isAdmin,
        ]);
    }

    public function store(StoreAreaRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        // Prepare coordinates
        $coordinates = null;
        if (isset($validated['coordinates_x']) || isset($validated['coordinates_y'])) {
            $coordinates = [
                'x' => $validated['coordinates_x'] ?? null,
                'y' => $validated['coordinates_y'] ?? null,
            ];
        }

        $area = new Area();
        $area->name = $validated['name'];
        $area->description = $validated['description'] ?? null;
        $area->garden_id = (int) $validated['garden_id'];
        $area->type = AreaTypeEnum::from($validated['type']);
        $area->size_sqm = $validated['size_sqm'] ?? null;
        $area->coordinates = $coordinates;
        $area->color = $validated['color'] ?? null;
        $area->is_active = $validated['is_active'] ?? true;
        $area->save();

        return redirect()
            ->route('areas.index')
            ->with('success', "Bereich '{$area->name}' wurde erfolgreich erstellt.");
    }
}
