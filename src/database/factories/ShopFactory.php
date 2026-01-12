<?php

namespace Database\Factories;

use App\Models\Shop;
use Illuminate\Database\Eloquent\Factories\Factory;

class ShopFactory extends Factory
{
    protected $model = Shop::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->company(),
            'area' => '東京都',
            'genre' => '寿司',
            'image_path' => null,
            'owner_id' => \App\Models\User::factory()->create(['role' => 'owner'])->id,
        ];
    }
}
