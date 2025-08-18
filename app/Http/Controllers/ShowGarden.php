<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Garden;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

final class ShowGarden extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request, Garden $garden): View
    {
        // Modern Laravel 12 authorization using Gate facade
        Gate::authorize('view', $garden);

        $garden->load(['user', 'plants']);

        return view('gardens.show', [
            'garden' => $garden,
        ]);
    }
}
