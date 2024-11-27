<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Exceptions\FormRequestException;
use Illuminate\Contracts\Validation\Validator;

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
            'tel_no'    => 'required|digits_between:10,11',
            'birthday'  => 'required|date_format:Y-m-d',
            'password'  => 'required|string|max:255',
            'gender'    => 'required|numeric|digits:1',
            'user_type' => 'required|numeric|digits:1'
        ];
    }

    public function messages(): array
    {
        // TODO
        return [
            
        ];
    }

    protected function failedValidation(Validator $validator)
    {

        throw new FormRequestException($validator->errors()->all());
    }
}
