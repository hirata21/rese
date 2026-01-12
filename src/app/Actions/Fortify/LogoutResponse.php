<?php

namespace App\Actions\Fortify;

use Laravel\Fortify\Contracts\LogoutResponse as LogoutResponseContract;

class LogoutResponse implements LogoutResponseContract
{
    public function toResponse($request)
    {
        if ($request->wantsJson()) {
            return response()->json([], 204);
        }

        // ✅ ルート名で出し分け（ログアウト後は guard 判定しづらいのでこれが安定）
        if ($request->routeIs('admin.logout')) {
            return redirect()->route('admin.login');
        }

        if ($request->routeIs('owner.logout')) {
            return redirect()->route('owner.login');
        }

        return redirect()->route('login');
    }
}
