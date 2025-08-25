<?php

declare(strict_types=1);

namespace App\Http\Controllers\Area;

use App\Http\Controllers\Controller;
use App\Services\Area\AreaService;
use App\Traits\AuthenticatedUser;

abstract class AreaController extends Controller
{
    use AuthenticatedUser;

    public function __construct(protected readonly AreaService $areaService) {}

}
