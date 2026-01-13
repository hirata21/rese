<?php

use Illuminate\Support\Facades\Storage;

if (! function_exists('shop_image_url')) {
    function shop_image_url($shop)
    {
        if (! $shop || ! $shop->image_path) {
            return asset('images/noimage.jpg');
        }

        // ✅ 環境ごとのFILESYSTEM_DISK（public / s3）に従う
        return Storage::url($shop->image_path);
    }
}
