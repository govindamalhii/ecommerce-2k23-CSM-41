<?php

namespace Tests\Feature\Admin;

use App\Models\Category;
use App\Models\Product;
use App\Models\Sku;
use App\Models\User;
use App\Models\Variant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create(['role' => 'admin']);
    }

    public function test_product_without_active_sku_cannot_be_published(): void
    {
        $product = Product::factory()->create(['status' => 'draft']);
        Variant::factory()->for($product)->create(); // no SKU attached

        $response = $this->actingAs($this->admin())
            ->patchJson("/api/v1/admin/products/{$product->id}", [
                'status' => 'published',
            ]);

        $response->assertStatus(422)->assertJsonValidationErrors('status');
    }

    public function test_product_with_active_sku_can_be_published(): void
    {
        $product = Product::factory()->create(['status' => 'draft']);
        $variant = Variant::factory()->for($product)->create();
        Sku::factory()->for($variant)->create(['is_active' => true]);

        $response = $this->actingAs($this->admin())
            ->patchJson("/api/v1/admin/products/{$product->id}", [
                'status' => 'published',
            ]);

        $response->assertOk();
        $this->assertSame('published', $product->fresh()->status);
    }

    public function test_duplicate_product_slug_is_rejected(): void
    {
        Product::factory()->create(['slug' => 'dune']);

        $response = $this->actingAs($this->admin())
            ->postJson('/api/v1/admin/products', [
                'category_id' => Category::factory()->create()->id,
                'name' => 'Dune',
                'slug' => 'dune',
            ]);

        $response->assertStatus(422)->assertJsonValidationErrors('slug');
    }
}
