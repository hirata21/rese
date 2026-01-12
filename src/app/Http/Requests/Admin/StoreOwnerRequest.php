<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreOwnerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

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
                'confirmed',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'      => '名前を入力してください。',
            'name.string'        => '名前は文字列で入力してください。',
            'name.max'           => '名前は191文字以内で入力してください。',

            'email.required'     => 'メールアドレスを入力してください。',
            'email.string'       => 'メールアドレスは文字列で入力してください。',
            'email.email'        => 'メールアドレスの形式が正しくありません。',
            'email.max'          => 'メールアドレスは191文字以内で入力してください。',
            'email.unique'       => 'このメールアドレスは既に使用されています。',

            'password.required'  => 'パスワードを入力してください。',
            'password.string'    => 'パスワードは文字列で入力してください。',
            'password.min'       => 'パスワードは8文字以上で入力してください。',
            'password.max'       => 'パスワードは191文字以内で入力してください。',
            'password.confirmed' => 'パスワードが一致しません。',
        ];
    }
}