<?php

namespace Tests\Feature\Admin;

use App\Models\Product;
use App\Models\Sku;
use App\Models\User;
use App\Models\Variant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VariantSkuTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create(['role' => 'admin']);
    }

    public function test_duplicate_variant_combination_is_rejected(): void
    {
        $product = Product::factory()->create();
        Variant::factory()->for($product)->create([
            'option_values' => ['format' => 'paperback'],
            'option_signature' => Variant::signatureFor(['format' => 'paperback']),
        ]);

        $response = $this->actingAs($this->admin())
            ->postJson("/api/v1/admin/products/{$product->id}/variants", [
                'option_values' => ['format' => 'paperback'],
            ]);

        $response->assertStatus(422)->assertJsonValidationErrors('option_values');
    }

    public function test_duplicate_sku_code_is_rejected(): void
    {
        $variant = Variant::factory()->create();
        Sku::factory()->for($variant)->create(['sku_code' => 'BOOK-DUNE-PBK-EN']);

        $product = $variant->product;
        $otherVariant = Variant::factory()->for($product)->create();

        $response = $this->actingAs($this->admin())
            ->postJson("/api/v1/admin/products/{$product->id}/skus", [
                'variant_id' => $otherVariant->id,
                'sku_code' => 'BOOK-DUNE-PBK-EN',
                'price' => 9.99,
                'stock_quantity' => 5,
            ]);

        $response->assertStatus(422)->assertJsonValidationErrors('sku_code');
    }

    public function test_stock_cannot_go_negative(): void
    {
        $sku = Sku::factory()->create(['stock_quantity' => 3]);

        $result = $sku->decrementStock(5);

        $this->assertFalse($result);
        $this->assertSame(3, $sku->fresh()->stock_quantity);
    }
}
