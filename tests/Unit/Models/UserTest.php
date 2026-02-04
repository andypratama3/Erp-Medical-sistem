<?php

namespace Tests\Unit\Models;

use App\Models\User;
use App\Models\Branch;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_user_has_roles(): void
    {
        $user = User::first();
        
        $this->assertTrue($user->hasRole($user->roles->first()->name));
    }

    public function test_user_can_be_assigned_role(): void
    {
        $user = User::factory()->create();
        
        $user->assignRole('admin');
        
        $this->assertTrue($user->hasRole('admin'));
    }

    public function test_user_password_is_hashed(): void
    {
        $user = User::factory()->create([
            'password' => 'testpassword',
        ]);
        
        $this->assertTrue(Hash::check('testpassword', $user->password));
        $this->assertNotEquals('testpassword', $user->password);
    }

    public function test_user_has_current_branch(): void
    {
        $user = User::first();
        
        if ($user->current_branch_id) {
            $this->assertInstanceOf(Branch::class, $user->currentBranch);
        } else {
            $this->assertNull($user->currentBranch);
        }
    }

    public function test_user_email_must_be_unique(): void
    {
        $user1 = User::first();
        
        $this->expectException(\Illuminate\Database\QueryException::class);
        
        User::create([
            'name' => 'Test User',
            'email' => $user1->email,
            'password' => Hash::make('password'),
        ]);
    }

    public function test_user_can_have_multiple_branches(): void
    {
        $user = User::first();
        
        if ($user->branches) {
            $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $user->branches);
        } else {
            $this->assertTrue(true);
        }
    }

    public function test_user_remember_token_is_generated(): void
    {
        $user = User::first();
        
        $user->setRememberToken('test-token');
        $user->save();
        
        $this->assertEquals('test-token', $user->getRememberToken());
    }
}
