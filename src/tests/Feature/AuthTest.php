<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_register_login_logout_flow(): void
    {
        // 登録（/register は route名無しなので直叩き）
        $this->post('/register', [
            'name' => 'テストユーザー',
            'email' => 'test@example.com',
            'password' => 'password123',
            // confirmed を使ってるなら追加
            // 'password_confirmation' => 'password123',
        ])->assertStatus(302);

        $this->assertDatabaseHas('users', ['email' => 'test@example.com']);

        // ログイン
        $this->post(route('login.post'), [
            'email' => 'test@example.com',
            'password' => 'password123',
        ])->assertStatus(302);

        // マイページに入れる
        $this->get(route('mypage'))->assertOk();

        // ログアウト
        $this->post(route('logout'))->assertStatus(302);

        // ログアウト後は弾かれる
        $this->get(route('mypage'))->assertStatus(302);
    }

    public function test_register_validation(): void
    {
        $this->post('/register', [
            'name' => '',
            'email' => 'a@example.com',
            'password' => 'password123',
        ])->assertSessionHasErrors(['name']);

        $this->post('/register', [
            'name' => 'A',
            'email' => 'not-mail',
            'password' => 'password123',
        ])->assertSessionHasErrors(['email']);

        $this->post('/register', [
            'name' => 'A',
            'email' => 'b@example.com',
            'password' => '1234567',
        ])->assertSessionHasErrors(['password']);
    }

    public function test_login_validation(): void
    {
        $this->post(route('login.post'), [
            'email' => '',
            'password' => '',
        ])->assertSessionHasErrors(['email', 'password']);
    }
}
