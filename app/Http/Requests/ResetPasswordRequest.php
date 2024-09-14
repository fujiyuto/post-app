<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use App\Exceptions\FormRequestException;

class ResetPasswordRequest extends FormRequest
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
            'new_password'              => 'required|string|confirmed',
            'new_password_confirmation' => 'required|string',
            'token'                     => 'required|string'
        ];
    }

    public function messages(): array
    {
        // TODO
        return [
            'new_password.required'         => 'パスワードは必須です',
            'new_password.string'           => 'パスワードは文字です',
            'new_password.confirmed'        => 'パスワードが一致しません',
            'new_password_confirm.required' => 'パスワード（確認用）は必須です',
            'token.required'                => 'トークンは必須です',
            'token.string'                  => 'トークンは文字列です'
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new FormRequestException($validator->errors()->all());
    }
}
