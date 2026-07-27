<?php

namespace App\Http\Requests\Cashier;

use Illuminate\Foundation\Http\FormRequest;

class StoreManualOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'customer_name'       => ['nullable', 'string', 'max:100'],
            'order_type'          => ['required', 'in:dine_in,take_away'],
            'restaurant_table_id' => ['required_if:order_type,dine_in', 'nullable', 'exists:restaurant_tables,id'],
            'payment_method'      => ['required', 'in:cash,qris'],
            'cash_amount'         => ['required_if:payment_method,cash', 'nullable', 'numeric', 'min:0'],
            'items'               => ['required', 'array', 'min:1'],
            'items.*.menu_item_id'=> ['required', 'integer', 'exists:menu_items,id'],
            'items.*.quantity'    => ['required', 'integer', 'min:1', 'max:99'],
            'items.*.notes'       => ['nullable', 'string', 'max:200'],
        ];
    }

    public function messages(): array
    {
        return [
            'restaurant_table_id.required_if' => 'Table selection is required for Dine In orders.',
            'cash_amount.required_if'         => 'Please enter the cash amount received.',
            'items.required'                  => 'At least one menu item must be added.',
            'items.min'                       => 'At least one menu item must be added.',
        ];
    }
}
