<?php

namespace Tests\Feature\Admin;

use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create(['role' => 'admin']);
    }

    public function test_admin_can_create_category(): void
    {
        $response = $this->actingAs($this->admin())
            ->postJson('/api/v1/admin/categories', [
                'name' => 'Fiction',
                'slug' => 'fiction',
            ]);

        $response->assertCreated();
        $this->assertDatabaseHas('categories', ['slug' => 'fiction']);
    }

    public function test_duplicate_slug_is_rejected_with_422(): void
    {
        Category::factory()->create(['slug' => 'fiction']);

        $response = $this->actingAs($this->admin())
            ->postJson('/api/v1/admin/categories', [
                'name' => 'Fiction Again',
                'slug' => 'fiction',
            ]);

        $response->assertStatus(422)->assertJsonValidationErrors('slug');
    }

    public function test_category_cannot_become_its_own_ancestor(): void
    {
        $a = Category::factory()->create();
        $b = Category::factory()->create(['parent_id' => $a->id]);
        $c = Category::factory()->create(['parent_id' => $b->id]);

        // A -> B -> C already exists; making C the parent of A would close
        // the loop (A -> B -> C -> A). Must be rejected.
        $response = $this->actingAs($this->admin())
            ->patchJson("/api/v1/admin/categories/{$a->id}", [
                'parent_id' => $c->id,
            ]);

        $response->assertStatus(422)->assertJsonValidationErrors('parent_id');
    }
}
