<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ProductFactory extends Factory
{
    public function definition(): array
    {
        $title = rtrim(fake()->unique()->sentence(3), '.');

        return [
            'category_id' => Category::factory(),
            'name' => $title,
            'slug' => Str::slug($title),
            'description' => fake()->paragraph(),
            'status' => 'draft',
            'specifications' => null,
        ];
    }
}
