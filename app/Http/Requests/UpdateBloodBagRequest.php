<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateBloodBagRequest extends FormRequest
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
            'bag_number' => 'required|string|max:50',
            'blood_group' => 'required|string|max:5',
            'donor_name' => 'required|string|max:100',
            'collection_date' => 'required|date',
            'expiry_date' => 'required|date|after:collection_date',
            'quantity' => 'required|integer|min:1',
            'status' => 'required|in:available,reserved,used,expired',
            'is_tested' => 'nullable|boolean',
            'is_secure' => 'nullable|boolean',
        ];
    }
}
