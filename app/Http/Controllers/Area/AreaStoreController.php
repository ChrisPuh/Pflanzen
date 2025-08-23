<?php

declare(strict_types=1);

namespace App\Http\Controllers\Area;

use App\DTOs\Area\AreaStoreDTO;
use App\Http\Requests\Area\AreaStoreRequest;
use Illuminate\Http\RedirectResponse;
use Throwable as ThrowableAlias;

final class AreaStoreController extends AreaController
{
    /**
     * @throws ThrowableAlias
     */
    public function __invoke(AreaStoreRequest $request): RedirectResponse
    {
        // TODO handle possible exceptions and provide user feedback
        $area = $this->areaService->storeArea(AreaStoreDTO::fromValidatedRequest($request->validated()));

        return redirect()
            ->route('areas.index')
            ->with('success', "Bereich '$area->name' wurde erfolgreich erstellt.");
    }
}
