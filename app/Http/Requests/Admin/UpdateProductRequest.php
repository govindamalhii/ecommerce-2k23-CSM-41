<?php

namespace App\Http\Requests\Admin;

use App\Rules\ValidFlatSpecJson;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $productId = (int) $this->route('product');

        return [
            'category_id' => ['sometimes', 'integer', 'exists:categories,id'],
            'name' => ['sometimes', 'string', 'max:180'],
            'slug' => ['sometimes', 'string', 'max:180', Rule::unique('products', 'slug')->ignore($productId)],
            'description' => ['nullable', 'string'],
            'status' => ['sometimes', 'in:draft,published,archived'],
            'specifications' => ['nullable', 'array', new ValidFlatSpecJson()],
        ];
    }
}
