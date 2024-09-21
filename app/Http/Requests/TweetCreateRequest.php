<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Exceptions\FormRequestException;
use Illuminate\Contracts\Validation\Validator;

class TweetCreateRequest extends FormRequest
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
            'restaurant_id' => 'required|numeric',
            'message'       => 'required|string'
        ];
    }

    public function messages(): array
    {
        return [
            'restaurant_id.required' => '店IDは必須',
            'restaurant_id.numeric'  => '店IDの書式が違う',
            'message.required'       => 'メッセージは必須',
            'message.string'         => 'メッセージは文字列'
        ];
    }

    public function failedValidation(Validator $validator)
    {
        throw new FormRequestException($validator->errors()->all());
    }
}
