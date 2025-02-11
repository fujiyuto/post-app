<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Exceptions\FormRequestException;
use Illuminate\Contracts\Validation\Validator;

class ReservationEditRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'reserve_date' => 'required',
            'reserve_time' => 'required',
            'num_of_people' => 'required',
            'notes' => ''
        ];
    }

    public function messages(): array
    {
        // TODO
        return [];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new FormRequestException($validator->errors()->all());
    }
}
