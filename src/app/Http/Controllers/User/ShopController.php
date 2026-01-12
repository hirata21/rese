<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Shop;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ShopController extends Controller
{
    /**
     * 店舗一覧 + 検索（公開）
     * GET /
     */
    public function index(Request $request): View
    {
        $area   = $request->query('area');
        $genre  = $request->query('genre');
        $search = $request->query('search');

        $areas = Shop::query()
            ->whereNotNull('area')
            ->select('area')
            ->distinct()
            ->orderBy('area')
            ->pluck('area');

        $genres = Shop::query()
            ->whereNotNull('genre')
            ->select('genre')
            ->distinct()
            ->orderBy('genre')
            ->pluck('genre');

        $shops = Shop::query()
            ->area($area)
            ->genre($genre)
            ->keyword($search)
            ->with('owner')
            ->withCount('favorites')
            ->withAvg('reservations', 'rating')
            ->orderByDesc('id')
            ->paginate(20)
            ->withQueryString();

        return view('shop.index', [
            'shops'  => $shops,
            'areas'  => $areas,
            'genres' => $genres,
            'area'   => $area,
            'genre'  => $genre,
            'search' => $search,
        ]);
    }

    /**
     *
     * GET /shops/{shop}
     */
    public function show(Shop $shop): View
    {
        return view('shop.detail', compact('shop'));
    }

    /**
     *
     * GET /detail/{shop}
     */
    public function detail(Shop $shop): View
    {
        return $this->show($shop);
    }
}