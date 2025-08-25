<?php

declare(strict_types=1);

namespace App\Http\Controllers\Area;

use App\Http\Controllers\Controller;
use App\Models\Area;
use App\Services\Area\AreaService;
use App\Traits\AuthenticatedUser;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

final class AreaShowController extends Controller
{
    use AuthenticatedUser;

    public function __construct(private readonly AreaService $areaService) {}

    public function __invoke(Request $request, Area $area): View
    {
        Gate::authorize('view', $area);

        $showData = $this->areaService->getShowData($area);

        return view('areas.show', $showData);
    }
}
