<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReservationRequest extends FormRequest
{
    /**
     * ログインユーザーのみ予約可
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * バリデーションルール
     */
    public function rules(): array
    {
        return [

            // 過去日付は禁止
            'date' => [
                'required',
                'date',
                'after_or_equal:today',
            ],

            'time' => [
                'required',
            ],

            'number' => [
                'required',
                'integer',
                'between:1,10',
            ],

            'course_id' => [
                'required',
            ],
        ];
    }

    /**
     * エラーメッセージ
     */
    public function messages(): array
    {
        return [
            'date.required'       => '日付を選択してください。',
            'date.date'           => '正しい日付を選択してください。',
            'date.after_or_equal' => '過去の日付は選択できません。',

            'time.required'       => '時間を選択してください。',

            'number.required'     => '人数を選択してください。',
            'number.integer'      => '人数は数値で指定してください。',
            'number.between'      => '人数は1〜10人で選択してください。',

            'course_id.required'  => 'コースを選択してください。',
        ];
    }

    /**
     * バリデーション前に値を整形
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'reserved_date'      => $this->date,
            'reserved_time'      => $this->time,
            'number_of_people'   => $this->number,
        ]);
    }
}
