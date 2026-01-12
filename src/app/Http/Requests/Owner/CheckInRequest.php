<?php

namespace App\Http\Requests\Owner;

use Illuminate\Foundation\Http\FormRequest;

class CheckInRequest extends FormRequest
{
    public function authorize(): bool
    {
        // ownerガードでログインしている前提
        return auth('owner')->check();
    }

    public function rules(): array
    {
        return [
            'token' => ['required', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'token.required' => 'QRコードが正しく読み取れませんでした。',
            'token.string'   => 'QRコードが不正です。',
        ];
    }
}
