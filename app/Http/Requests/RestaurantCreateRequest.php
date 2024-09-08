<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RestaurantCreateRequest extends FormRequest
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
            'restaurant_name' => 'required|string|max:255',
            'zip_cd'          => 'required|string|max:7',
            'address'         => 'required|string|max:255',
            'email'           => 'nullable|email:rfc,dns|max:255',
            'tel_no'          => 'required|string|max:255',
            'price_min'       => 'nullable|numeric',
            'price_max'       => 'nullable|numeric',
        ];
    }

    public function messages(): array
    {
        // TODO
        return [];
    }
}
