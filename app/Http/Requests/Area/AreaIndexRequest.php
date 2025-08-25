<?php

declare(strict_types=1);

namespace App\Http\Requests\Area;

use App\DTOs\Area\AreaIndexFilterDTO;
use Illuminate\Foundation\Http\FormRequest;

final class AreaIndexRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return AreaIndexFilterDTO::rules();
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return AreaIndexFilterDTO::messages();
    }

    public function toDTO(): AreaIndexFilterDTO
    {
        return AreaIndexFilterDTO::fromRequest($this);
    }
}
