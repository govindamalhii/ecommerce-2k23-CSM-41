<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Variant extends Model
{
    use HasFactory;

    protected $fillable = ['product_id', 'option_values', 'option_signature'];

    protected function casts(): array
    {
        return ['option_values' => 'array'];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function skus(): HasMany
    {
        return $this->hasMany(Sku::class);
    }

    /**
     * Canonical signature for a set of option values (CAT04): sorts by key
     * first so {color: black, size: M} and {size: M, color: black} always
     * hash identically, then the DB unique index on
     * (product_id, option_signature) blocks true duplicates.
     */
    public static function signatureFor(array $optionValues): string
    {
        ksort($optionValues);

        return md5(json_encode($optionValues));
    }
}
