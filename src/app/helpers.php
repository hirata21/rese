<?php

use Illuminate\Support\Facades\Storage;

if (! function_exists('shop_image_url')) {
    function shop_image_url($shop)
    {
        if (! $shop) {
            return asset('images/noimage.jpg');
        }

        if (! $shop->image_path) {
            return asset('images/noimage.jpg');
        }

        return Storage::disk('public')->url($shop->image_path);
    }
}
