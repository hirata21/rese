<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreOwnerRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    /**
     * 店舗代表者（オーナー）作成画面
     */
    public function createOwner()
    {
        return view('admin.owner-create');
    }

    /**
     * 店舗代表者（オーナー）登録処理
     */
    public function storeOwner(StoreOwnerRequest $request)
    {
        $data = $request->validated();

        User::create([
            'name'              => $data['name'],
            'email'             => $data['email'],
            'password'          => Hash::make($data['password']),
            'role'              => 'owner',
            'email_verified_at' => now(),
        ]);

        return redirect()
            ->route('admin.owners.create')
            ->with('status', '店舗代表者（オーナー）を作成しました。');
    }
}
