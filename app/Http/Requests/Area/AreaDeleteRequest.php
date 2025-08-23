<?php

declare(strict_types=1);

namespace App\Http\Requests\Area;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

final class AreaDeleteRequest extends FormRequest
{
    public function authorize(): bool
    {
        $area = $this->route('area');

        Gate::authorize('delete', $area);

        return true;
    }
}
