<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\User;

class OwnerOnly
{
    public function handle(Request $request, Closure $next)
    {
        
        if (!auth()->check()) {
            return redirect()->route('owner.login');
        }

        // ★ここを定数で統一
        if (auth()->user()->role !== User::ROLE_OWNER) {
            abort(403, 'このアクションは許可されていません。');
        }

        return $next($request);
    }
}
