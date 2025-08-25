<?php

declare(strict_types=1);

namespace App\Http\Controllers\Area;

use App\Http\Controllers\Controller;
use App\Services\Area\AreaService;
use App\Traits\AuthenticatedUser;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

final class AreaCreateController extends Controller
{
    use AuthenticatedUser;

    public function __construct(private readonly AreaService $areaService) {}

    public function __invoke(Request $request): View
    {
        ['user' => $user, 'isAdmin' => $isAdmin] = $this->getUserAndAdminStatus();

        $createData = $this->areaService->getCreateData($user, $isAdmin, $request->integer('garden_id'));

        return view('areas.create', $createData);
    }
}
