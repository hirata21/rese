<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Contracts\LoginResponse;
use Laravel\Fortify\Contracts\LogoutResponse;

class AdminLoginController extends Controller
{
    public function __construct()
    {
        // 管理者ログイン画面は未ログイン(admin)のみ
        $this->middleware('guest:admin')->only(['showLoginForm', 'login']);
    }

    /**
     * 管理者ログイン画面
     */
    public function showLoginForm()
    {
        return view('admin.login');
    }

    /**
     * 管理者ログイン処理（adminガード）
     */
    public function login(LoginRequest $request, LoginResponse $response)
    {
        $credentials = $request->only(['email', 'password']);
        $remember = $request->boolean('remember');

        if (!Auth::guard('admin')->attempt($credentials, $remember)) {
            throw ValidationException::withMessages([
                'email' => ['メールアドレスまたはパスワードが正しくありません。'],
            ]);
        }

        $request->session()->regenerate();

        return $response->toResponse($request);
    }

    /**
     * 管理者ログアウト
     */
    public function logout(Request $request, LogoutResponse $response)
    {
        Auth::guard('admin')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return $response->toResponse($request);
    }
}