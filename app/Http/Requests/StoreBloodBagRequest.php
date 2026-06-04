<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBloodBagRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'refrigerator_id' => [
                'required',
                'exists:refrigerators,id'
            ],

            'bag_number' => [
                'required',
                'string',
                'max:50',
                'unique:blood_bags,bag_number'
            ],

            'blood_group' => [
                'required',
                'in:A+,A-,B+,B-,AB+,AB-,O+,O-'
            ],

            'donor_name' => [
                'required',
                'string',
                'max:255'
            ],

            'collection_date' => [
                'required',
                'date'
            ],

            'expiry_date' => [
                'required',
                'date',
                'after:collection_date'
            ],

            'quantity' => [
                'required',
                'numeric',
                'min:1'
            ],

            'status' => [
                'required',
                'in:available,reserved,used,expired'
            ],

            'is_tested' => [
                'required',
                'accepted'
            ],

            'is_secure' => [
                'required',
                'accepted'
            ]
        ];
    }
}
