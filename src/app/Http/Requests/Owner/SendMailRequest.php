<?php

namespace App\Http\Requests\Owner;

use Illuminate\Foundation\Http\FormRequest;

class SendMailRequest extends FormRequest
{
    /**
     * 認可
     */
    public function authorize(): bool
    {
        // owner ガードでログインしている想定
        return auth()->check();
    }

    /**
     * バリデーションルール
     */
    public function rules(): array
    {
        return [
            'subject' => ['required', 'string', 'max:100'],
            'body'    => ['required', 'string', 'max:2000'],
        ];
    }

    /**
     * バリデーションメッセージ
     */
    public function messages(): array
    {
        return [
            'subject.required' => '件名を入力してください。',
            'subject.max'      => '件名は100文字以内で入力してください。',

            'body.required'    => '本文を入力してください。',
            'body.max'         => '本文は2000文字以内で入力してください。',
        ];
    }
}
