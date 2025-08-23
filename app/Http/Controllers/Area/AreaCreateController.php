<?php

declare(strict_types=1);

namespace App\Http\Controllers\Area;

use App\DTOs\Area\AreaCreateDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Area\StoreAreaRequest;
use App\Services\AreaService;
use App\Traits\AuthenticatedUser;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

final class AreaCreateController extends Controller
{
    use AuthenticatedUser;

    public function __construct(private readonly AreaService $areaService) {}

    public function create(Request $request): View
    {
        ['user' => $user, 'isAdmin' => $isAdmin] = $this->getUserAndAdminStatus();

        $createData = $this->areaService->getCreateData($user, $isAdmin, $request->integer('garden_id'));

        return view('areas.create', $createData);
    }

    public function store(StoreAreaRequest $request): RedirectResponse
    {
        $area = $this->areaService->createArea(AreaCreateDTO::fromRequest($request->validated()));

        return redirect()
            ->route('areas.index')
            ->with('success', "Bereich '{$area->name}' wurde erfolgreich erstellt.");
    }
}
