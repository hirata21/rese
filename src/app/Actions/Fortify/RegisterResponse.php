<?php

namespace App\Actions\Fortify;

use Laravel\Fortify\Contracts\RegisterResponse as RegisterResponseContract;

class RegisterResponse implements RegisterResponseContract
{
    /**
     * 登録成功後のレスポンス
     */
    public function toResponse($request)
    {
        // 登録成功後はメール認証画面へ
        return redirect()->route('verification.notice');
    }
}
