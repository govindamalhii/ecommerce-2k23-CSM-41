<?php

namespace App\Http\Requests\Admin;

use App\Rules\CategoryNotOwnAncestor;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $categoryId = (int) $this->route('category');

        return [
            'name' => ['sometimes', 'string', 'max:150'],
            'slug' => ['sometimes', 'string', 'max:150', Rule::unique('categories', 'slug')->ignore($categoryId)],
            'parent_id' => ['sometimes', 'nullable', 'integer', 'exists:categories,id', new CategoryNotOwnAncestor($categoryId)],
            'is_active' => ['sometimes', 'boolean'], // deactivate = PATCH { is_active: false }
        ];
    }
}
