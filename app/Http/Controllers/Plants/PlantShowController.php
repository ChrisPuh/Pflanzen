<?php

declare(strict_types=1);

namespace App\Http\Controllers\Plants;

use App\Http\Controllers\Controller;
use App\Http\Requests\Plants\PlantShowRequest;
use App\Models\Plant;
use App\Services\PlantService;
use Illuminate\View\View;

final class PlantShowController extends Controller
{
    public function __construct(
        private readonly PlantService $plantService
    ) {}

    public function __invoke(PlantShowRequest $request, Plant $plant): View
    {
        $showData = $this->plantService->getShowData($plant);

        return view('plants.show', $showData);
    }
}
