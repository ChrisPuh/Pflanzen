<?php

declare(strict_types=1);

namespace App\Http\Controllers\Area;

use App\Enums\Area\AreaTypeEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\Area\IndexAreaRequest;
use App\Models\Area;
use App\Models\Garden;
use App\Traits\AuthenticatedUser;
use Illuminate\Contracts\View\View;

final class AreasIndexController extends Controller
{
    use AuthenticatedUser;

    public function __invoke(IndexAreaRequest $request): View
    {
        $user = $this->getUser();
        $isAdmin = $this->isAdmin();

        // Base query for areas belonging to user's gardens
        $areasQuery = Area::query()
            ->with(['garden:id,name,type', 'plants:id,name'])
            ->whereHas('garden', function (\Illuminate\Database\Eloquent\Builder $query) use ($isAdmin, $user): void {
                if ($isAdmin) {
                    // Admin can see all areas
                    return;
                }

                $query->where('user_id', $user->id);
            })
            ->latest();

        // Apply filters
        if ($request->filled('garden_id') && $request->get('garden_id') !== '') {
            $areasQuery->where('garden_id', $request->integer('garden_id'));
        }

        if ($request->filled('type') && $request->get('type') !== '') {
            $areasQuery->where('type', $request->string('type'));
        }

        if ($request->filled('category') && $request->get('category') !== '') {
            $areasQuery->byCategory($request->string('category'));
        }

        if ($request->filled('search') && $request->get('search') !== '') {
            $search = $request->string('search');
            $areasQuery->where(function (\Illuminate\Database\Eloquent\Builder $query) use ($search): void {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('active') && $request->get('active') !== '') {
            $activeValue = $request->get('active');
            if ($activeValue === '1') {
                $areasQuery->active();
            } elseif ($activeValue === '0') {
                $areasQuery->where('is_active', false);
            }
            // If empty string, show all (no filter applied)
        }

        $areas = $areasQuery->paginate(12)->withQueryString();

        // Get user's gardens for filter dropdown
        $userGardens = Garden::query()
            ->when(! $isAdmin, function (\Illuminate\Database\Eloquent\Builder $query) use ($user): void {
                $query->where('user_id', $user->id);
            })
            ->select('id', 'name', 'type')
            ->orderBy('name')
            ->get();

        // Get area statistics
        $totalAreas = Area::query()
            ->whereHas('garden', function (\Illuminate\Database\Eloquent\Builder $query) use ($isAdmin, $user): void {
                if (! $isAdmin) {
                    $query->where('user_id', $user->id);
                }
            })
            ->count();

        $activeAreas = Area::query()
            ->active()
            ->whereHas('garden', function (\Illuminate\Database\Eloquent\Builder $query) use ($isAdmin, $user): void {
                if (! $isAdmin) {
                    $query->where('user_id', $user->id);
                }
            })
            ->count();

        $plantingAreas = Area::query()
            ->whereIn('type', [
                AreaTypeEnum::FlowerBed->value,
                AreaTypeEnum::VegetableBed->value,
                AreaTypeEnum::HerbBed->value,
                AreaTypeEnum::Meadow->value,
                AreaTypeEnum::TreeArea->value,
            ])
            ->whereHas('garden', function (\Illuminate\Database\Eloquent\Builder $query) use ($isAdmin, $user): void {
                if (! $isAdmin) {
                    $query->where('user_id', $user->id);
                }
            })
            ->count();

        // Format garden options for select component
        $gardenOptions = $userGardens->mapWithKeys(function (Garden $garden) use ($isAdmin) {
            $label = $garden->name;
            if ($isAdmin) {
                $label .= ' ('.$garden->type->getLabel().')';
            }

            return [$garden->id => $label];
        });

        // Format area type options for select component
        $areaTypeOptions = collect(AreaTypeEnum::options())->mapWithKeys(function (array $type) {
            return [$type['value'] => $type['label']];
        });

        // Format area category options for select component
        $areaCategoryOptions = collect(AreaTypeEnum::cases())
            ->map(fn (AreaTypeEnum $type): string => $type->category())
            ->unique()
            ->values()
            ->mapWithKeys(function (string $category) {
                return [$category => $category];
            });

        return view('areas.index', [
            'areas' => $areas,
            'gardenOptions' => $gardenOptions,
            'areaTypeOptions' => $areaTypeOptions,
            'areaCategoryOptions' => $areaCategoryOptions,
            'isAdmin' => $isAdmin,
            'totalAreas' => $totalAreas,
            'activeAreas' => $activeAreas,
            'plantingAreas' => $plantingAreas,
            'filters' => [
                'garden_id' => $request->integer('garden_id'),
                'type' => $request->string('type'),
                'category' => $request->string('category'),
                'search' => $request->string('search'),
                'active' => $request->has('active') ? $request->boolean('active') : null,
            ],
        ]);
    }
}
