<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasFactory;

    protected $fillable = ['parent_id', 'name', 'slug', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    /**
     * True if assigning $candidateParentId as this category's parent would
     * create a cycle (this category is $candidateParentId itself, or an
     * ancestor of it, at any depth). A plain self-referencing FK can only
     * block a direct self-reference — this covers the multi-level case.
     */
    public function wouldCreateCycleWith(int $candidateParentId): bool
    {
        if ($candidateParentId === $this->id) {
            return true;
        }

        $ancestor = Category::find($candidateParentId);

        while ($ancestor !== null) {
            if ($ancestor->id === $this->id) {
                return true;
            }
            $ancestor = $ancestor->parent;
        }

        return false;
    }
}
