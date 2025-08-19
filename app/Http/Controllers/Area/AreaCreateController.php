<?php

declare(strict_types=1);

namespace App\Http\Controllers\Area;

use App\Enums\Area\AreaTypeEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\Area\StoreAreaRequest;
use App\Models\Area;
use App\Models\Garden;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

final class AreaCreateController extends Controller
{
    public function create(Request $request): View
    {
        $user = $request->user();

        // Get user's gardens for dropdown
        $userGardens = Garden::query()
            ->when(! $user->hasRole('admin'), function (\Illuminate\Database\Eloquent\Builder $query) use ($user): void {
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
            'isAdmin' => $user->hasRole('admin'),
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

        $area = Area::create([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'garden_id' => $validated['garden_id'],
            'type' => AreaTypeEnum::from($validated['type']),
            'size_sqm' => $validated['size_sqm'] ?? null,
            'coordinates' => $coordinates,
            'color' => $validated['color'] ?? null,
            'is_active' => $validated['is_active'] ?? true,
        ]);

        return redirect()
            ->route('areas.index')
            ->with('success', "Bereich '{$area->name}' wurde erfolgreich erstellt.");
    }
}
