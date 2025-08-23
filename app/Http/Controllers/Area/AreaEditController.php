<?php

declare(strict_types=1);

namespace App\Http\Controllers\Area;

use App\Http\Controllers\Controller;
use App\Models\Area;
use App\Services\AreaService;
use App\Traits\AuthenticatedUser;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Gate;

final class AreaEditController extends Controller
{
    use AuthenticatedUser;

    public function __construct(private readonly AreaService $areaService) {}

    public function __invoke(Area $area): View
    {
        Gate::authorize('update', $area);

        ['user' => $user, 'isAdmin' => $isAdmin] = $this->getUserAndAdminStatus();

        $editData = $this->areaService->getEditData($user, $area, $isAdmin);

        return view('areas.edit', $editData);
    }
}
