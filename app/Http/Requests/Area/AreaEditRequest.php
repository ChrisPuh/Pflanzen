<?php

declare(strict_types=1);

namespace App\Http\Requests\Area;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

final class AreaEditRequest extends FormRequest
{
    public function authorize(): bool
    {
        $area = $this->route('area');

        Gate::authorize('update', $area);

        return true;
    }
}
