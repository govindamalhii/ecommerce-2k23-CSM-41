<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Sku extends Model
{
    use HasFactory;

    protected $fillable = ['variant_id', 'sku_code', 'price', 'stock_quantity', 'is_active'];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    public function variant(): BelongsTo
    {
        return $this->belongsTo(Variant::class);
    }

    /**
     * Atomically decrement stock, guaranteeing it never goes negative
     * regardless of concurrent requests. Returns false (no-op) if there
     * isn't enough stock to cover $qty.
     */
    public function decrementStock(int $qty): bool
    {
        $affected = static::where('id', $this->id)
            ->where('stock_quantity', '>=', $qty)
            ->decrement('stock_quantity', $qty);

        return $affected > 0;
    }
}
