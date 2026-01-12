<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Contracts\LoginResponse;
use Laravel\Fortify\Contracts\LogoutResponse;

class OwnerLoginController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest:owner')->only(['showLoginForm', 'login']);
    }

    public function showLoginForm()
    {
        return view('owner.login');
    }

    public function login(LoginRequest $request, LoginResponse $response)
    {
        $credentials = $request->only(['email', 'password']);
        $remember = $request->boolean('remember');

        if (!Auth::guard('owner')->attempt($credentials, $remember)) {
            throw ValidationException::withMessages([
                'email' => ['メールアドレス、またはパスワードが違います。'],
            ]);
        }

        $request->session()->regenerate();

        return $response->toResponse($request);
    }

    public function logout(Request $request, LogoutResponse $response)
    {
        Auth::guard('owner')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return $response->toResponse($request);
    }
}
