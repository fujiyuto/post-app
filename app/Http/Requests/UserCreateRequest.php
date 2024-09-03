<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserCreateRequest extends FormRequest
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
            'email'     => 'required|email:rfc,dns|max:255',
            'password'  => 'required|string|max:255',
            'gender'    => 'required|numeric|digits:1',
            'user_type' => 'required|numeric|digits:1'
        ];
    }

    public function messages(): array
    {
        // TODO
        return [];
    }
}
