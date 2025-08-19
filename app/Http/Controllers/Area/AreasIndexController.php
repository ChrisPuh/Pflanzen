<?php

declare(strict_types=1);

namespace App\Http\Controllers\Area;

use App\Enums\Area\AreaTypeEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\Area\IndexAreaRequest;
use App\Models\Area;
use App\Models\Garden;
use Illuminate\Contracts\View\View;

final class AreasIndexController extends Controller
{
    public function __invoke(IndexAreaRequest $request): View
    {
        $user = $request->user();

        // Base query for areas belonging to user's gardens
        $areasQuery = Area::query()
            ->with(['garden:id,name,type', 'plants:id,name'])
            ->whereHas('garden', function (\Illuminate\Database\Eloquent\Builder $query) use ($user): void {
                if ($user->hasRole('admin')) {
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
            ->when(! $user->hasRole('admin'), function (\Illuminate\Database\Eloquent\Builder $query) use ($user): void {
                $query->where('user_id', $user->id);
            })
            ->select('id', 'name', 'type')
            ->orderBy('name')
            ->get();

        // Get area statistics
        $totalAreas = Area::query()
            ->whereHas('garden', function (\Illuminate\Database\Eloquent\Builder $query) use ($user): void {
                if (! $user->hasRole('admin')) {
                    $query->where('user_id', $user->id);
                }
            })
            ->count();

        $activeAreas = Area::query()
            ->active()
            ->whereHas('garden', function (\Illuminate\Database\Eloquent\Builder $query) use ($user): void {
                if (! $user->hasRole('admin')) {
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
            ->whereHas('garden', function (\Illuminate\Database\Eloquent\Builder $query) use ($user): void {
                if (! $user->hasRole('admin')) {
                    $query->where('user_id', $user->id);
                }
            })
            ->count();

        return view('areas.index', [
            'areas' => $areas,
            'userGardens' => $userGardens,
            'areaTypes' => AreaTypeEnum::options(),
            'areaCategories' => collect(AreaTypeEnum::cases())
                ->map(fn (AreaTypeEnum $type): string => $type->category())
                ->unique()
                ->values(),
            'isAdmin' => $user->hasRole('admin'),
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
