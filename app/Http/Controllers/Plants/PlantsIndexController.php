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
        // TODO implement Data Transfer Object (DTO) for filters
        $filters = [
            'search' => $request->getSearch(),
            'type' => $request->getType(),
            'categories' => $request->getCategories(),
        ];

        $indexData = $this->plantService->getIndexData($filters);

        return view('plants.index', $indexData);
    }
}
