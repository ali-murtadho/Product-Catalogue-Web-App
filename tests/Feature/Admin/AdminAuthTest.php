<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_unauthenticated_user_is_redirected_to_login_from_admin_dashboard(): void
    {
        $response = $this->get('/admin');

        $response->assertRedirect('/login');
    }

    public function test_unauthenticated_user_is_redirected_to_login_from_admin_products(): void
    {
        $response = $this->get('/admin/products');

        $response->assertRedirect('/login');
    }

    public function test_unauthenticated_user_is_redirected_to_login_from_admin_categories(): void
    {
        $response = $this->get('/admin/categories');

        $response->assertRedirect('/login');
    }

    public function test_unauthenticated_user_is_redirected_to_login_from_admin_settings(): void
    {
        $response = $this->get('/admin/settings');

        $response->assertRedirect('/login');
    }

    public function test_authenticated_user_can_access_admin_dashboard(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/admin');

        $response->assertStatus(200);
    }

    public function test_authenticated_user_can_access_admin_products(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/admin/products');

        $response->assertStatus(200);
    }

    public function test_authenticated_user_can_access_admin_categories(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/admin/categories');

        $response->assertStatus(200);
    }

    public function test_authenticated_user_can_access_admin_settings(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/admin/settings');

        $response->assertStatus(200);
    }
}
