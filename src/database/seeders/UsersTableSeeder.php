<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Carbon;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // ▼ 管理者
        DB::table('users')->insert([
            'name'              => '管理者',
            'email'             => 'admin@example.com',
            'password'          => Hash::make('admin123'),
            'role'              => 'admin',
            'email_verified_at' => now(),
            'created_at'        => now(),
            'updated_at'        => now(),
        ]);

        // ▼ オーナー（3名）
        $owners = [
            ['name' => 'オーナー1', 'email' => 'owner1@example.com'],
            ['name' => 'オーナー2', 'email' => 'owner2@example.com'],
            ['name' => 'オーナー3', 'email' => 'owner3@example.com'],
        ];

        foreach ($owners as $owner) {
            DB::table('users')->insert([
                'name'              => $owner['name'],
                'email'             => $owner['email'],
                'password'          => Hash::make('owner123'),
                'role'              => 'owner',
                'email_verified_at' => now(),
                'created_at'        => now(),
                'updated_at'        => now(),
            ]);
        }

        // ▼ 一般ユーザー（10名）
        for ($i = 1; $i <= 10; $i++) {
            DB::table('users')->insert([
                'name'              => "ユーザー{$i}",
                'email'             => "user{$i}@example.com",
                'password'          => Hash::make('password'),
                'role'              => 'user',
                'email_verified_at' => now(),
                'created_at'        => now(),
                'updated_at'        => now(),
            ]);
        }
    }
}
