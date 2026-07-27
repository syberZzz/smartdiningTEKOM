<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTableRequest extends FormRequest
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
     */
    public function rules(): array
    {
        return [
            'table_number' => 'required|string|max:10|unique:restaurant_tables,table_number',
            'capacity'     => 'required|integer|min:1|max:20',
            'status'       => 'required|in:available,disabled',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'table_number.required' => 'Table number is required.',
            'table_number.unique'   => 'This table number already exists.',
            'capacity.required'     => 'Capacity is required.',
            'capacity.integer'      => 'Capacity must be a whole number.',
            'capacity.min'          => 'Capacity must be at least 1.',
            'capacity.max'          => 'Capacity cannot exceed 20.',
        ];
    }
}
