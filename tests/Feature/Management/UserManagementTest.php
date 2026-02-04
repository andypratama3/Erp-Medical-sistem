<?php

namespace Tests\Feature\Management;

use App\Models\User;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_can_view_users_index(): void
    {
        $user = User::first();
        $user->assignRole('admin');
        
        $response = $this->actingAs($user)
            ->get(route('management.users.index'));

        $response->assertStatus(200);
    }

    public function test_can_create_user(): void
    {
        $admin = User::first();
        $admin->assignRole('admin');
        $role = Role::first();

        $response = $this->actingAs($admin)
            ->post(route('management.users.store'), [
                'name' => 'New User',
                'email' => 'newuser@example.com',
                'password' => 'password',
                'password_confirmation' => 'password',
                'role' => $role->name,
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('users', [
            'email' => 'newuser@example.com',
        ]);
    }

    public function test_can_update_user(): void
    {
        $admin = User::first();
        $admin->assignRole('admin');
        $user = User::skip(1)->first();

        $response = $this->actingAs($admin)
            ->put(route('management.users.update', $user), [
                'name' => 'Updated Name',
                'email' => $user->email,
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Updated Name',
        ]);
    }

    public function test_can_toggle_user_status(): void
    {
        $admin = User::first();
        $admin->assignRole('admin');
        $user = User::skip(1)->first();

        $response = $this->actingAs($admin)
            ->post(route('management.users.toggle-status', $user));

        $response->assertRedirect();
    }

    public function test_can_reset_user_password(): void
    {
        $admin = User::first();
        $admin->assignRole('admin');
        $user = User::skip(1)->first();

        $response = $this->actingAs($admin)
            ->post(route('management.users.reset-password', $user), [
                'password' => 'newpassword',
                'password_confirmation' => 'newpassword',
            ]);

        $response->assertRedirect();
    }

    public function test_can_view_roles_index(): void
    {
        $admin = User::first();
        $admin->assignRole('admin');
        
        $response = $this->actingAs($admin)
            ->get(route('management.roles.index'));

        $response->assertStatus(200);
    }

    public function test_can_create_role(): void
    {
        $admin = User::first();
        $admin->assignRole('admin');
        $permissions = Permission::take(3)->pluck('name')->toArray();

        $response = $this->actingAs($admin)
            ->post(route('management.roles.store'), [
                'name' => 'test-role',
                'permissions' => $permissions,
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('roles', [
            'name' => 'test-role',
        ]);
    }

    public function test_can_update_role(): void
    {
        $admin = User::first();
        $admin->assignRole('admin');
        $role = Role::first();
        $permissions = Permission::take(2)->pluck('name')->toArray();

        $response = $this->actingAs($admin)
            ->put(route('management.roles.update', $role), [
                'name' => $role->name,
                'permissions' => $permissions,
            ]);

        $response->assertRedirect();
    }

    public function test_can_view_permissions_index(): void
    {
        $admin = User::first();
        $admin->assignRole('admin');
        
        $response = $this->actingAs($admin)
            ->get(route('management.permissions.index'));

        $response->assertStatus(200);
    }

    public function test_unauthorized_user_cannot_access_management(): void
    {
        $user = User::factory()->create();
        
        $response = $this->actingAs($user)
            ->get(route('management.users.index'));

        $response->assertStatus(403);
    }
}
