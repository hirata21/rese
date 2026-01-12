<?php

namespace Tests\Feature;

use App\Models\Shop;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class StorageImageTest extends TestCase
{
    use RefreshDatabase;

    public function test_shop_image_upload_creates_shop_record(): void
    {
        Storage::fake('public');

        /** @var \App\Models\User $owner */
        $owner = User::factory()->createOne(['role' => 'owner']);
        $this->actingAs($owner, 'owner');

        $file = UploadedFile::fake()->image('shop.jpg');

        $res = $this->post(route('owner.shop.store'), [
            'name'  => 'テスト店',
            'area'  => '東京都',
            'genre' => '寿司',
            'description' => 'テスト用の店舗説明です。',
            'image' => $file,
        ]);

        $res->assertStatus(302);
        $res->assertSessionHasNoErrors();

        $shop = Shop::latest('id')->first();
        $this->assertNotNull($shop);

        if (Schema::hasColumn('shops', 'image_path')) {
            $this->assertNotEmpty($shop->image_path);
        }
    }
}
