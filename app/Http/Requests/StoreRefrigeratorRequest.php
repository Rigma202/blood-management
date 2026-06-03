<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreRefrigeratorRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'blood_bank_id' => [
                'required',
                'exists:blood_banks,id'
            ],

            'name' => [
                'required',
                'string',
                'max:100'
            ],

            'serial_number' => [
                'required',
                'string',
                'max:100',
                'unique:refrigerators,serial_number'
            ],

            'is_active' => [
                'required',
                'boolean'
            ]
        ];
    }
}
