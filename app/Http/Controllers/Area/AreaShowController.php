<?php

declare(strict_types=1);

namespace App\Http\Controllers\Area;

use App\Http\Controllers\Controller;
use App\Models\Area;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

final class AreaShowController extends Controller
{
    public function __invoke(Request $request, Area $area): View
    {
        $user = $request->user();
        
        // Check access permissions
        if (!$user->hasRole('admin') && $area->garden->user_id !== $user->id) {
            abort(403, 'Sie haben keine Berechtigung, diesen Bereich anzuzeigen.');
        }

        $area->load(['garden', 'plants']);

        return view('areas.show', compact('area'));
    }
}
