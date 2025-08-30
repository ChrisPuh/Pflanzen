<?php

declare(strict_types=1);

namespace App\Http\Controllers\Area;

use App\Http\Requests\Area\AreaStoreRequest;
use Illuminate\Http\RedirectResponse;
use Throwable;

final class AreaStoreController extends AreaController
{
    /**
     * @throws Throwable
     */
    public function __invoke(AreaStoreRequest $request): RedirectResponse
    {
        // TODO handle possible exceptions and provide user feedback
        try {
            $area = $this->areaService->storeArea($request->toDTO());

            return redirect()
                ->route('areas.index')
                ->with('success', "Bereich '$area->name' wurde erfolgreich erstellt.");
        } catch (Throwable $exception) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Fehler beim Erstellen des Bereichs: '.$exception->getMessage());
        }
    }
}
