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

        // TODO implement Data Transfer Object (DTO::fromRequest) pattern here
        $filters = [
            'garden_id' => $request->filled('garden_id') ? $request->integer('garden_id') : null,
            'type' => $request->filled('type') ? $request->string('type') : null,
            'category' => $request->filled('category') ? $request->string('category') : null,
            'search' => $request->filled('search') ? $request->string('search') : null,
            'active' => $request->has('active') ? $request->boolean('active') : null,
        ];

        $indexData = $this->areaService->getIndexData($user, $filters, $isAdmin);

        return view('areas.index', $indexData);
    }
}
