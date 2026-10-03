<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id', 'name', 'slug', 'description', 'status', 'specifications',
    ];

    protected function casts(): array
    {
        return ['specifications' => 'array'];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function variants(): HasMany
    {
        return $this->hasMany(Variant::class);
    }

    public function activeSkuCount(): int
    {
        return Sku::whereIn('variant_id', $this->variants()->pluck('id'))
            ->where('is_active', true)
            ->count();
    }

    /**
     * CAT02/CAT03 rule: a product can only move to "published" once it has
     * at least one active, sellable SKU.
     */
    public function canPublish(): bool
    {
        return $this->activeSkuCount() > 0;
    }
}
