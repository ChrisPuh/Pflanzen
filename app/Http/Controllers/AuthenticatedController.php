<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Traits\AuthenticatedUser;

abstract class AuthenticatedController extends Controller
{
    use AuthenticatedUser;
}
