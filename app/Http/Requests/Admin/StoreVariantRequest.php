<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreVariantRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'option_values' => ['required', 'array', 'min:1'],
            'option_values.*' => ['required', 'string'],
        ];
    }
}
