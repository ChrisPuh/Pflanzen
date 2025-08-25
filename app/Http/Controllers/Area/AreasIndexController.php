<?php

declare(strict_types=1);

namespace App\Http\Controllers\Area;

use App\Http\Controllers\Controller;
use App\Http\Requests\Area\IndexAreaRequest;
use App\Services\Area\AreaService;
use App\Traits\AuthenticatedUser;
use Illuminate\Contracts\View\View;

final class AreasIndexController extends Controller
{
    use AuthenticatedUser;

    public function __construct(private readonly AreaService $areaService) {}

    public function __invoke(IndexAreaRequest $request): View
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
