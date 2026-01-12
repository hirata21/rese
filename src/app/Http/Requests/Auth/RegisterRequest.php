<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    /**
     * 認可
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * バリデーションルール
     */
    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:191',
            ],
            'email' => [
                'required',
                'string',
                'email',
                'max:191',
                'unique:users,email',
            ],
            'password' => [
                'required',
                'string',
                'min:8',
                'max:191',
            ],
        ];
    }

    /**
     * エラーメッセージ
     */
    public function messages(): array
    {
        return [
            'name.required'     => 'ユーザー名を入力してください。',
            'name.string'       => 'ユーザー名は文字列で入力してください。',
            'name.max'          => 'ユーザー名は191文字以内で入力してください。',

            'email.required'    => 'メールアドレスを入力してください。',
            'email.string'      => 'メールアドレスは文字列で入力してください。',
            'email.email'       => 'メールアドレスの形式で入力してください。',
            'email.max'         => 'メールアドレスは191文字以内で入力してください。',
            'email.unique'      => 'このメールアドレスは既に登録されています。',

            'password.required' => 'パスワードを入力してください。',
            'password.string'   => 'パスワードは文字列で入力してください。',
            'password.min'      => 'パスワードは8文字以上で入力してください。',
            'password.max'      => 'パスワードは191文字以内で入力してください。',
        ];
    }
}
