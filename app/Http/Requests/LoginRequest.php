<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Exceptions\FormRequestException;
use Illuminate\Contracts\Validation\Validator;

class LoginRequest extends FormRequest
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
            'user_name' => 'required|string|max:255',
            'password'  => 'required|string|max:255',
        ];
    }

    public function messages(): array
    {
        // TODO
        return [];
    }

    public function attributes(): array
    {
        return [
            'user_name' => 'ユーザー名',
            'password'  => 'パスワード'
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new FormRequestException($validator->errors()->all());
    }
}
