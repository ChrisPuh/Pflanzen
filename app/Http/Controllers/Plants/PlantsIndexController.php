<?php

declare(strict_types=1);

namespace App\Http\Controllers\Plants;

use App\Enums\PlantCategoryEnum;
use App\Enums\PlantTypeEnum;
use App\Http\Controllers\Controller;
use App\Models\Plant;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class PlantsIndexController extends Controller
{
    public function __invoke(Request $request): View
    {
        $search = $request->string('search')->value();
        $type = $request->string('type')->value();
        $categories = $request->array('categories');

        $plants = Plant::query()
            ->with(['plantType', 'categories'])
            ->when($search, fn (Builder $query, string $search): Builder => $query->where(fn (Builder $query): Builder => $query->where('name', 'like', "%{$search}%")
                ->orWhere('latin_name', 'like', "%{$search}%")
                ->orWhere('description', 'like', "%{$search}%")))
            ->when($type, fn (Builder $query, string $type): Builder => $query->whereHas('plantType', function (Builder $query) use ($type): Builder {
                // Convert string value to enum instance for comparison
                $enumValue = PlantTypeEnum::from($type);

                return $query->where('name', $enumValue);
            }))
            ->when($categories, fn (Builder $query, array $categories): Builder => $query->whereHas('categories', function (Builder $query) use ($categories): Builder {
                // Convert string values to enum instances for comparison
                $enumValues = array_map(fn (string $value): PlantCategoryEnum => PlantCategoryEnum::from($value), $categories);

                return $query->whereIn('name', $enumValues);
            }))
            ->orderBy('name')
            ->paginate(12)
            ->withQueryString();

        return view('plants.index', [
            'plants' => $plants,
            'search' => $search,
            'selectedType' => $type,
            'selectedCategories' => $categories,
            'plantTypes' => PlantTypeEnum::cases(),
            'plantCategories' => PlantCategoryEnum::cases(),
        ]);
    }
}
