<?php

namespace Database\Factories;

use App\Models\Variant;
use Illuminate\Database\Eloquent\Factories\Factory;

class SkuFactory extends Factory
{
    public function definition(): array
    {
        return [
            'variant_id' => Variant::factory(),
            'sku_code' => strtoupper(fake()->unique()->bothify('SKU-????-####')),
            'price' => fake()->randomFloat(2, 4.99, 49.99),
            'stock_quantity' => fake()->numberBetween(0, 100),
            'is_active' => true,
        ];
    }
}
