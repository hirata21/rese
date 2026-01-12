<?php

namespace Tests\Feature;

use App\Models\Favorite;
use App\Models\Shop;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FavoriteTest extends TestCase
{
    use RefreshDatabase;

    public function test_favorite_add_and_remove(): void
    {
        /** @var \App\Models\User $user */
        $user = User::factory()->createOne();
        $shop = Shop::factory()->createOne();

        $this->actingAs($user);

        $this->post(route('favorite.store', $shop))->assertStatus(302);

        // favorites テーブルがある前提（ログにも出てる）
        $this->assertDatabaseHas('favorites', [
            'user_id' => $user->id,
            'shop_id' => $shop->id,
        ]);

        $this->delete(route('favorite.destroy', $shop))->assertStatus(302);

        $this->assertDatabaseMissing('favorites', [
            'user_id' => $user->id,
            'shop_id' => $shop->id,
        ]);
    }
}