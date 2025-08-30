<?php

declare(strict_types=1);

namespace App\Http\Controllers\Area;

use App\Http\Requests\Area\AreaDeleteRequest;
use App\Models\Area;
use Exception;
use Illuminate\Http\RedirectResponse;
use Throwable;

final class AreaDeleteController extends AreaController
{
    /**
     * Soft delete the specified area.
     *
     * @param  AreaDeleteRequest  $request  holds the validated request data for deleting an area
     */
    public function __invoke(AreaDeleteRequest $request, Area $area): RedirectResponse
    {
        $dto = $request->toDTO();
        try {
            $this->areaService->deleteArea($dto);

            return redirect()
                ->route('areas.index')
                ->with('success', "Bereich '$dto->name' wurde erfolgreich gelöscht.");
        } catch (Exception|Throwable $exception) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', "Fehler beim Löschen des Bereichs '$dto->name': ".$exception->getMessage());
        }
    }
}
