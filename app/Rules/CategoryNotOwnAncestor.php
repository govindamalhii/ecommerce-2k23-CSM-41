<?php

namespace App\Rules;

use App\Models\Category;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * Blocks a parent_id that would make a category its own ancestor at any
 * depth (A -> B -> C -> A), not just a direct self-reference — that part
 * a foreign key alone can't express.
 */
class CategoryNotOwnAncestor implements ValidationRule
{
    public function __construct(private readonly ?int $categoryId) {}

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if ($this->categoryId === null || $value === null) {
            return; // new category, or clearing the parent — no cycle possible
        }

        $category = Category::find($this->categoryId);

        if ($category && $category->wouldCreateCycleWith((int) $value)) {
            $fail('This parent assignment would create a category cycle.');
        }
    }
}
