<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Favorite;
use App\Models\Shop;
use App\Models\User;

class FavoriteController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * お気に入り一覧
     */
    public function index()
    {
        /** @var User $user */
        $user = auth()->user();

        $shops = $user->favoriteShops()->get();

        return view('favorites.index', compact('shops'));
    }

    /**
     * お気に入り登録
     */
    public function store(Shop $shop)
    {
        Favorite::firstOrCreate([
            'user_id' => auth()->id(),
            'shop_id' => $shop->id,
        ]);

        return back();
    }

    /**
     * お気に入り削除
     */
    public function destroy(Shop $shop)
    {
        Favorite::where('user_id', auth()->id())
            ->where('shop_id', $shop->id)
            ->delete();

        return back();
    }
}