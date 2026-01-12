<?php

namespace Tests\Feature;

use App\Models\Shop;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShopTest extends TestCase
{
    use RefreshDatabase;

    public function test_shop_index_and_detail(): void
    {
        $shop = Shop::factory()->create();

        $this->get(route('shops.index'))->assertOk();
        $this->get(route('detail', $shop))->assertOk();
        $this->get(route('shops.show', $shop))->assertOk();
    }

    public function test_search_by_area_genre_name_query_params(): void
    {
        Shop::factory()->create(['area' => '東京都', 'genre' => '寿司', 'name' => 'Sushi A']);
        Shop::factory()->create(['area' => '大阪府', 'genre' => '焼肉', 'name' => 'Yakiniku B']);

        // ここは実装のクエリ名に合わせて変更（一般的な例）
        $this->get(route('shops.index', ['area' => '東京都']))->assertOk();
        $this->get(route('shops.index', ['genre' => '寿司']))->assertOk();
        $this->get(route('shops.index', ['keyword' => 'Sushi']))->assertOk();
    }
}
