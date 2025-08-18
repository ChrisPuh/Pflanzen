<?php

declare(strict_types=1);

namespace App\Http\Controllers\Garden;

use App\Http\Controllers\Controller;
use App\Models\Garden;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

final class GardensIndexController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request): View
    {
        Gate::authorize('viewAny', Garden::class);

        /** @var User $user */
        $user = $request->user();

        // Get user's gardens with filtering and pagination
        $gardens = Garden::query()

            ->when(! $user->hasRole('admin'), function (Builder $query) use ($user): void {
                // Non-admin users only see their own gardens
                $query->forUser($user);
            })
            ->with(['user', 'plants'])
            ->latest()
            ->paginate(12);

        return view('gardens.index', [
            'gardens' => $gardens,
            'isAdmin' => $user->hasRole('admin'),
        ]);
    }
}
