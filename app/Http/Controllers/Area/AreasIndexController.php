<?php

declare(strict_types=1);

namespace App\Http\Controllers\Area;

use App\Http\Requests\Area\AreaIndexRequest;
use Illuminate\Contracts\View\View;

final class AreasIndexController extends AreaController
{
    public function __invoke(AreaIndexRequest $request): View
    {
        ['user' => $user, 'isAdmin' => $isAdmin] = $this->getUserAndAdminStatus();

        $filter = $request->toDTO();

        $indexData = $this->areaService->getIndexData($user, $filter, $isAdmin);

        return view('areas.index', $indexData);
    }
}
