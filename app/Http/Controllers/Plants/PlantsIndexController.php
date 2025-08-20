<?php

declare(strict_types=1);

namespace App\Http\Controllers\Plants;

use App\Http\Controllers\Controller;
use App\Http\Requests\Plants\PlantsIndexRequest;
use App\Services\PlantService;
use Illuminate\View\View;

final class PlantsIndexController extends Controller
{
    public function __construct(
        private readonly PlantService $plantService
    ) {}

    public function __invoke(PlantsIndexRequest $request): View
    {
        $search = $request->getSearch();
        $type = $request->getType();
        $categories = $request->getCategories();

        $plants = $this->plantService->getFilteredPlants(
            search: $search,
            type: $type,
            categories: $categories
        );

        $plantTypes = $this->plantService->getAvailablePlantTypes();
        $plantCategories = $this->plantService->getAvailablePlantCategories();

        // Format plant types for select component
        $plantTypesOptions = collect($plantTypes)->mapWithKeys(function ($plantType) {
            return [$plantType->value => $plantType->getLabel()];
        });

        // Format plant categories for checkbox options
        $plantCategoriesOptions = collect($plantCategories)->mapWithKeys(function ($category) {
            return [$category->value => $category->getLabel()];
        });

        return view('plants.index', [
            'plants' => $plants,
            'search' => $search,
            'selectedType' => $type,
            'selectedCategories' => $categories,
            'plantTypesOptions' => $plantTypesOptions,
            'plantCategoriesOptions' => $plantCategoriesOptions,
            // Statistics for the layout
            'stats' => $this->plantService->getPlantStatistics(),
        ]);
    }
}
