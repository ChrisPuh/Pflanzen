<?php

declare(strict_types=1);

namespace App\Http\Controllers\Area;

use App\Http\Requests\Area\AreaEditRequest;
use App\Models\Area;
use Illuminate\Contracts\View\View;

final class AreaEditController extends AreaController
{
    public function __invoke(AreaEditRequest $request, Area $area): View
    {
        ['user' => $user, 'isAdmin' => $isAdmin] = $this->getUserAndAdminStatus();

        $editData = $this->areaService->getEditData($user, $area, $isAdmin);

        return view('areas.edit', $editData);
    }
}
