<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TableRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'code' => ['required', 'string', 'max:20', 'regex:/^[a-z0-9_-]+$/', Rule::unique('restaurant_tables')->ignore($this->route('table'))],
            'capacity' => ['required', 'integer', 'between:1,6'],
            'is_active' => ['required', 'boolean'],
        ];
    }
}
