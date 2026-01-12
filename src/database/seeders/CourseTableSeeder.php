<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Course;
use App\Models\Shop;

class CourseTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // すべての店舗に対してコースを作成
        $shops = Shop::all();

        foreach ($shops as $shop) {

            Course::create([
                'shop_id'        => $shop->id,
                'name'           => 'スタンダードコース',
                'price'          => 5000,
                'payment_method' => Course::PAYMENT_CASH,
                'is_active'      => true,
            ]);

            Course::create([
                'shop_id'        => $shop->id,
                'name'           => 'プレミアムコース',
                'price'          => 8000,
                'payment_method' => Course::PAYMENT_CARD,
                'is_active'      => true,
            ]);

            Course::create([
                'shop_id'        => $shop->id,
                'name'           => 'スペシャルコース',
                'price'          => 10000,
                'payment_method' => Course::PAYMENT_CARD,
                'is_active'      => true,
            ]);
        }
    }
}
