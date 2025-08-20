<?php

declare(strict_types=1);

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Http\Requests\Settings\PasswordUpdateRequest;
use App\Services\UserService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class PasswordController extends Controller
{
    public function __construct(private readonly UserService $userService) {}

    public function edit(Request $request): View
    {
        return view('settings.password', [
            'user' => $request->user(),
        ]);
    }

    public function update(PasswordUpdateRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $this->userService->updatePassword($request->user(), $validated['password']);

        return back()->with('status', 'password-updated');
    }
}
