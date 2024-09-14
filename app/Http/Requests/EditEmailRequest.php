<?php

namespace App\Http\Requests;

use App\Exceptions\FormRequestException;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class EditEmailRequest extends FormRequest
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
            'new_email' => [
                'required',
                'email:rfc,dns',
                'confirmed',
                Rule::notIn(Auth::user()->email)
            ],
            'new_email_confirmation' => [
                'required',
                'email:rfc,dns'
            ],
            'token' => [
                'required',
                'string'
            ]
        ];
    }

    public function messages(): array
    {
        // TODO
        return [
            'new_email.required'              => 'メールアドレスは必須です',
            'new_email.email'                 => 'メールアドレス形式が正しくないです',
            'new_email.max'                   => 'メールアドレスは最大255文字です',
            'new_email.confirmed'             => 'メールアドレスが一致しません',
            'new_email.not_in'                => '新しいメールアドレスを設定してください',
            'new_email_confirmation.required' => 'メールアドレス（確認用）は必須です',
            'new_email_confirmation.email'    => 'メールアドレス（確認用）の形式が正しくありません',
            'token.required'                  => 'トークンは必須です。',
            'token.string'                    => 'トークンは文字列です。'
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new FormRequestException($validator->errors()->all());
    }
}
