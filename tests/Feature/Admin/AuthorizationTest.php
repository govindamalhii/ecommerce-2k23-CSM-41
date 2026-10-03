<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_unauthenticated_request_is_rejected(): void
    {
        $response = $this->postJson('/api/v1/admin/categories', [
            'name' => 'Fiction',
            'slug' => 'fiction',
        ]);

        $response->assertStatus(401);
    }

    public function test_non_admin_user_is_rejected(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);

        $response = $this->actingAs($customer)
            ->postJson('/api/v1/admin/categories', [
                'name' => 'Fiction',
                'slug' => 'fiction',
            ]);

        $response->assertStatus(403);
    }

    public function test_admin_user_is_allowed(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)
            ->postJson('/api/v1/admin/categories', [
                'name' => 'Fiction',
                'slug' => 'fiction',
            ]);

        $response->assertCreated();
    }
}
