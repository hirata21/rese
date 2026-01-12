<?php

namespace App\Actions\Fortify;

use Illuminate\Support\Facades\Auth;
use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;

class LoginResponse implements LoginResponseContract
{
    public function toResponse($request)
    {
        if ($request->wantsJson()) {
            return response()->json([], 204);
        }

        // ✅ どのガードでログインしているかで遷移先を分ける
        if (Auth::guard('admin')->check()) {
            return redirect()->intended(route('admin.owners.create')); // ←あなたの管理者トップに合わせて
        }

        if (Auth::guard('owner')->check()) {
            return redirect()->intended(route('owner.dashboard')); // ←合わせて
        }

        return redirect()->intended(route('shops.index')); // user
    }
}