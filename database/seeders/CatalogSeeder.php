<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\Sku;
use App\Models\Variant;
use Illuminate\Database\Seeder;

class CatalogSeeder extends Seeder
{
    public function run(): void
    {
        $books = Category::create(['name' => 'Books', 'slug' => 'books']);
        $fiction = Category::create(['name' => 'Fiction', 'slug' => 'fiction', 'parent_id' => $books->id]);

        // --- Dune: two variants, one SKU each ---
        $dune = Product::create([
            'category_id' => $fiction->id,
            'name' => 'Dune',
            'slug' => 'dune',
            'description' => "Frank Herbert's classic science-fiction novel.",
            'status' => 'published',
        ]);

        Sku::create([
            'variant_id' => $this->makeVariant($dune, ['format' => 'paperback', 'language' => 'english'])->id,
            'sku_code' => 'BOOK-DUNE-PBK-EN',
            'price' => 12.99,
            'stock_quantity' => 40,
        ]);

        Sku::create([
            'variant_id' => $this->makeVariant($dune, ['format' => 'ebook', 'language' => 'english'])->id,
            'sku_code' => 'BOOK-DUNE-EBK-EN',
            'price' => 7.99,
            'stock_quantity' => 999,
        ]);

        // --- Atomic Habits: single default variant, one SKU ---
        $habits = Product::create([
            'category_id' => $books->id,
            'name' => 'Atomic Habits',
            'slug' => 'atomic-habits',
            'description' => 'James Clear on building better habits.',
            'status' => 'published',
        ]);

        Sku::create([
            'variant_id' => $this->makeVariant($habits, ['format' => 'paperback'])->id,
            'sku_code' => 'BOOK-HABITS-PBK',
            'price' => 14.50,
            'stock_quantity' => 25,
        ]);

        // --- The Pragmatic Programmer: paperback is sellable; the
        // audiobook variant intentionally gets NO SKU — the unavailable
        // combination required by the Sprint 2 brief. The product still
        // has an active SKU overall (the paperback), so it's allowed to
        // stay published, demonstrating one sellable + one unavailable
        // variant on the same product.
        $pragprog = Product::create([
            'category_id' => $books->id,
            'name' => 'The Pragmatic Programmer',
            'slug' => 'the-pragmatic-programmer',
            'description' => 'Hunt & Thomas on software craftsmanship.',
            'status' => 'published',
        ]);

        Sku::create([
            'variant_id' => $this->makeVariant($pragprog, ['format' => 'paperback'])->id,
            'sku_code' => 'BOOK-PRAGPROG-PBK',
            'price' => 34.99,
            'stock_quantity' => 15,
        ]);

        $this->makeVariant($pragprog, ['format' => 'audiobook']); // no SKU, on purpose
    }

    private function makeVariant(Product $product, array $optionValues): Variant
    {
        return Variant::create([
            'product_id' => $product->id,
            'option_values' => $optionValues,
            'option_signature' => Variant::signatureFor($optionValues),
        ]);
    }
}
