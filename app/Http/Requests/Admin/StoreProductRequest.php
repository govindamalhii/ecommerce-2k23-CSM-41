<?php

namespace App\Http\Requests\Admin;

use App\Rules\ValidFlatSpecJson;
use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:180'],
            'slug' => ['required', 'string', 'max:180', 'unique:products,slug'],
            'description' => ['nullable', 'string'],
            'status' => ['sometimes', 'in:draft,published,archived'],
            'specifications' => ['nullable', 'array', new ValidFlatSpecJson()],
        ];
    }
}
