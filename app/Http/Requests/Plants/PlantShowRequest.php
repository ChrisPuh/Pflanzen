<?php

declare(strict_types=1);

namespace App\Http\Requests\Plants;

use Illuminate\Foundation\Http\FormRequest;

final class PlantShowRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<mixed>>
     */
    public function rules(): array
    {
        return [
            // No additional query parameters to validate for show page
            // The Plant model binding is handled by Laravel's route model binding
        ];
    }
}
