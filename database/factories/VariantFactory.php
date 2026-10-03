<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\Variant;
use Illuminate\Database\Eloquent\Factories\Factory;

class VariantFactory extends Factory
{
    public function definition(): array
    {
        $options = ['format' => fake()->randomElement(['paperback', 'ebook', 'audiobook'])];

        return [
            'product_id' => Product::factory(),
            'option_values' => $options,
            'option_signature' => Variant::signatureFor($options),
        ];
    }
}
