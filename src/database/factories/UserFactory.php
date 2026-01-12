<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;

class UserFactory extends Factory
{
    protected $model = User::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name'              => $this->faker->name(),
            'email'             => $this->faker->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password'          => Hash::make('password'),
            'role'              => 'user',
            'remember_token'    => Str::random(10),
        ];
    }

    /**
     * メール未認証ユーザー
     */
    public function unverified()
    {
        return $this->state(fn() => [
            'email_verified_at' => null,
        ]);
    }

    /**
     * 管理者ユーザー
     */
    public function admin()
    {
        return $this->state(fn() => [
            'role' => 'admin',
        ]);
    }

    /**
     * オーナーユーザー
     */
    public function owner()
    {
        return $this->state(fn() => [
            'role' => 'owner',
        ]);
    }
}
