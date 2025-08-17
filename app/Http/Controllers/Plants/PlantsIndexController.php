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

        return view('plants.index', [
            'plants' => $plants,
            'search' => $search,
            'selectedType' => $type,
            'selectedCategories' => $categories,
            'plantTypes' => $this->plantService->getAvailablePlantTypes(),
            'plantCategories' => $this->plantService->getAvailablePlantCategories(),
        ]);
    }
}
