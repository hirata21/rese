<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AdminOnly
{
    public function handle(Request $request, Closure $next)
    {
        if (!auth()->check()) {
            return redirect()->route('admin.login');
        }

        if (auth()->user()->role !== 'admin') {
            // 403にしたいなら abort(403) でもOK
            abort(403, 'このアクションは許可されていません。');
        }

        return $next($request);
    }
}
