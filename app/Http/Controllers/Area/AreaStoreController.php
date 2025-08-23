<?php

declare(strict_types=1);

namespace App\Http\Controllers\Area;

use App\DTOs\Area\AreaStoreDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Area\AreaStoreRequest;
use App\Services\AreaService;
use Illuminate\Http\RedirectResponse;
use OpenSpout\Common\Exception\InvalidArgumentException;

final class AreaStoreController extends Controller
{
    public function __construct(
        private readonly AreaService $areaService
    ) {}

    /**
     * @throws InvalidArgumentException
     */
    public function __invoke(AreaStoreRequest $request): RedirectResponse
    {
        // TODO handle possible exceptions and provide user feedback
        $area = $this->areaService->storeArea(AreaStoreDTO::fromValidatedRequest($request->validated()));

        return redirect()
            ->route('areas.index')
            ->with('success', "Bereich '{$area->name}' wurde erfolgreich erstellt.");
    }
}
