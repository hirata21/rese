<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Http\Requests\Owner\ShopRequest;
use App\Models\Shop;
use App\Models\Course;
use Illuminate\Support\Facades\Storage;

class ShopController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:owner');
    }

    /**
     * 店舗作成画面
     */
    public function create()
    {
        $ownerId = auth('owner')->id();

        $shop = Shop::where('owner_id', $ownerId)->first();

        // 既に店舗があれば編集画面へ
        if ($shop) {
            return redirect()->route('owner.shop.edit');
        }

        return view('owner.shop-form', [
            'shop' => null,
            'mode' => 'create',
        ]);
    }

    /**
     * 店舗保存
     */
    public function store(ShopRequest $request)
    {
        $ownerId = auth('owner')->id();

        // 二重作成防止（要件に合わせて）
        if (Shop::where('owner_id', $ownerId)->exists()) {
            return redirect()
                ->route('owner.shop.edit')
                ->with('status', 'すでに店舗情報が登録されています。更新してください。');
        }

        $data = $request->validated();
        $data['owner_id'] = $ownerId;

        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->storePublicly('shops');
        }

        // ✅ 店舗作成
        $shop = Shop::create($data);

        // ✅ デフォルトコース自動作成（Seederと同じ構造に合わせる）
        if ($shop->courses()->count() === 0) {
            $shop->courses()->createMany([
                [
                    'name'           => 'スタンダードコース',
                    'price'          => 5000,
                    'payment_method' => Course::PAYMENT_CASH,
                    'is_active'      => true,
                ],
                [
                    'name'           => 'プレミアムコース',
                    'price'          => 8000,
                    'payment_method' => Course::PAYMENT_CARD,
                    'is_active'      => true,
                ],
                [
                    'name'           => 'スペシャルコース',
                    'price'          => 10000,
                    'payment_method' => Course::PAYMENT_CARD,
                    'is_active'      => true,
                ],
            ]);
        }

        return redirect()
            ->route('owner.dashboard')
            ->with('status', '店舗情報を作成しました。');
    }

    /**
     * 店舗編集画面
     */
    public function edit()
    {
        $shop = $this->ownerShopOrFail();

        return view('owner.shop-form', [
            'shop' => $shop,
            'mode' => 'edit',
        ]);
    }

    /**
     * 店舗更新
     */
    public function update(ShopRequest $request)
    {
        $shop = $this->ownerShopOrFail();
        $data = $request->validated();

        if ($request->hasFile('image')) {
            if (!empty($shop->image_path)) {
                Storage::delete($shop->image_path);
            }

            $data['image_path'] = $request->file('image')->storePublicly('shops');
        }

        $shop->update($data);

        return redirect()
            ->route('owner.shop.edit')
            ->with('status', '店舗情報を更新しました。');
    }

    private function ownerShopOrFail(): Shop
    {
        $ownerId = auth('owner')->id();

        return Shop::where('owner_id', $ownerId)->firstOrFail();
    }
}
