<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Exceptions\FormRequestException;
use Illuminate\Contracts\Validation\Validator;

class PostCreateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title'          => 'required|string',
            'content'        => 'required|string',
            'visited_at'     => 'nullable|date',
            'period_of_time' => 'required|digits:1|min:1,max:2',
            'points'         => 'required|min:0|max:5',
            'price_min'      => 'nullable|numeric',
            'price_max'      => 'nullable|numeric|gt:price_min',
            'image_url1'     => 'nullable|image',
            'image_url2'     => 'nullable|image',
            'image_url3'     => 'nullable|image',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required'          => 'タイトルは必須',
            'title.string'            => 'タイトルは文字列',
            'content.required'        => '内容は必須',
            'content.string'          => '内容は文字列',
            'visited_at.date'         => '訪問日は日付',
            'period_of_time.required' => '時間帯は必須',
            'period_of_time.digits'   => '時間帯は桁数1',
            'period_of_time.min'      => '時間帯は最小値1',
            'period_of_time.max'      => '時間帯は最大値2',
            'points.required'         => '点数は必須',
            'points.min'              => '点数は最小値0',
            'points.max'              => '点数は最大値5',
            'price_min.numeric'       => '価格（最小）は数値',
            'price_max.numeric'       => '価格（最大）は数値',
            'price_max.gt'            => '価格（最大）は価格（最小）より大きい'
        ];
    }

    public function failedValidation(Validator $validator)
    {
        return response()->json([
            'error' => 'エラーです'
        ], 400);
    }
}
