<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminOwnerCreateTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_owner(): void
    {
        /** @var \App\Models\User $admin */
        // ★ admin guard が別モデルの場合はここを合わせる必要あり
        $admin = User::factory()->create([
            'role' => 'admin',
            'password' => Hash::make('password123'),
        ]);

        $this->actingAs($admin, 'admin');

        $this->post(route('admin.owners.store'), [
            'name' => '店舗代表者',
            'email' => 'owner1@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ])->assertStatus(302);

        $this->assertDatabaseHas('users', ['email' => 'owner1@example.com']);
    }
}
