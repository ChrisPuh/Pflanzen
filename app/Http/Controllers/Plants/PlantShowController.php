<?php

declare(strict_types=1);

namespace App\Http\Controllers\Plants;

use App\Http\Controllers\Controller;
use App\Models\Plant;
use Illuminate\View\View;

final class PlantShowController extends Controller
{
    public function __invoke(Plant $plant): View
    {
        // Load relationships for the plant detail view
        $plant->load(['plantType', 'categories']);

        return view('plants.show', [
            'plant' => $plant,
        ]);
    }
}
