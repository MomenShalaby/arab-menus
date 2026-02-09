<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validates restaurant search/filter form data.
 */
class SearchRestaurantRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'search' => ['nullable', 'string', 'max:255'],
            'city_id' => ['nullable', 'integer', 'exists:cities,id'],
            'zone_id' => ['nullable', 'integer', 'exists:zones,id'],
            'category_id' => ['nullable', 'integer', 'exists:categories,id'],
            'sort' => ['nullable', 'string', 'in:name,views,latest'],
            'direction' => ['nullable', 'string', 'in:asc,desc'],
        ];
    }

    /**
     * Get the validated filters.
     *
     * @return array<string, mixed>
     */
    public function filters(): array
    {
        return $this->only(['search', 'city_id', 'zone_id', 'category_id', 'sort', 'direction']);
    }
}
