<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => [
                'bail',
                'required',
                'string',
                'email',
                'max:191',
            ],
            'password' => [
                'bail',
                'required',
                'string',
                'min:8',
                'max:191',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'email.required'    => 'メールアドレスを入力してください。',
            'email.string'      => 'メールアドレスは文字列で入力してください。',
            'email.email'       => 'メールアドレスの形式で入力してください。',
            'email.max'         => 'メールアドレスは191文字以内で入力してください。',

            'password.required' => 'パスワードを入力してください。',
            'password.string'   => 'パスワードは文字列で入力してください。',
            'password.min'      => 'パスワードは8文字以上で入力してください。',
            'password.max'      => 'パスワードは191文字以内で入力してください。',
        ];
    }
}
