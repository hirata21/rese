<?php

namespace App\Http\Requests\Owner;

use Illuminate\Foundation\Http\FormRequest;

class ShopRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'image'       => ['nullable', 'image', 'mimes:jpeg,png', 'max:5120'],
            'name'        => ['required', 'string', 'max:255'],
            'area'        => ['required', 'string', 'max:255'],
            'genre'       => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'image.image'     => '画像ファイルを選択してください。',
            'image.mimes'     => '画像はJPEGまたはPNG形式でアップロードしてください。',
            'image.max'       => '画像サイズは5MB以内にしてください。',

            'name.required'   => '店舗名を入力してください。',
            'area.required'   => 'エリアを入力してください。',
            'genre.required'  => 'ジャンルを入力してください。',
            'description.required' => '店舗説明を入力してください。',
        ];
    }
}
