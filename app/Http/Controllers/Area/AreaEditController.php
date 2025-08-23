<?php

declare(strict_types=1);

namespace App\Http\Controllers\Area;

use App\Http\Controllers\Controller;
use App\Http\Requests\Area\AreaUpdateRequest;
use App\Models\Area;
use App\Services\AreaService;
use App\Traits\AuthenticatedUser;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;

final class AreaEditController extends Controller
{
    use AuthenticatedUser;

    public function __construct(private readonly AreaService $areaService) {}

    public function edit(Area $area): View
    {
        Gate::authorize('update', $area);

        ['user' => $user, 'isAdmin' => $isAdmin] = $this->getUserAndAdminStatus();

        $editData = $this->areaService->getEditData($user, $area, $isAdmin);

        return view('areas.edit', $editData);
    }

    public function update(AreaUpdateRequest $request, Area $area): RedirectResponse
    {
        // TODO implement Data Transfer Object (DTO) pattern for request data
        $area = $this->areaService->updateArea($area, $request->validated());

        return redirect()
            ->route('areas.show', $area)
            ->with('success', "Bereich '{$area->name}' wurde erfolgreich aktualisiert.");
    }
}
