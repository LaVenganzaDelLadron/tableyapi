<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class ApiFormRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function messages(): array
    {
        return [
            'required' => 'Please provide :attribute.',
            'required_with' => 'Please provide :attribute when :values is present.',
            'email' => 'Please enter a valid email address.',
            'unique' => 'This :attribute is already in use.',
            'exists' => 'The selected :attribute does not exist.',
            'in' => 'Please select a valid :attribute.',
            'string' => ':Attribute must be text.',
            'integer' => ':Attribute must be a whole number.',
            'numeric' => ':Attribute must be a valid number.',
            'decimal' => ':Attribute must be a valid amount with up to 2 decimal places.',
            'boolean' => ':Attribute must be true or false.',
            'date' => ':Attribute must be a valid date.',
            'after_or_equal' => ':Attribute must be on or after :date.',
            'min.numeric' => ':Attribute must be at least :min.',
            'min.integer' => ':Attribute must be at least :min.',
            'max.string' => ':Attribute may not be greater than :max characters.',
            'confirmed' => 'Password confirmation does not match.',
            'array' => ':Attribute must be a list.',
            'items.*.product_id.exists' => 'The selected product does not exist.',
            'items.*.quantity.min' => 'Each order item quantity must be at least 1.',
        ];
    }

    public function attributes(): array
    {
        return [
            'user_id' => 'customer',
            'category_id' => 'category',
            'cart_id' => 'cart',
            'product_id' => 'product',
            'order_id' => 'order',
            'supplier_id' => 'supplier',
            'employee_id' => 'employee',
            'employee_attendance_id' => 'employee attendance',
            'cacao_purchase_id' => 'cacao purchase',
            'cacao_batch_id' => 'cacao batch',
            'production_batch_id' => 'production batch',
            'price_per_kilogram' => 'price per kilogram',
            'raw_kilogram' => 'raw cacao weight in kilograms',
            'roasted_kilogram' => 'roasted cacao weight in kilograms',
            'kilogram' => 'cacao weight in kilograms',
            'sack_count' => 'sack count',
            'roasting_payment_per_sack' => 'roasting payment per sack',
            'total_roasting_payment' => 'total roasting payment',
            'packs_produced' => 'tableya packs produced',
            'price_per_pack' => 'price per pack',
            'total_production_value' => 'total production value',
            'price_type' => 'price type',
            'sub_total' => 'subtotal',
            'shipping_fee' => 'shipping fee',
            'total_price' => 'total price',
            'payment_status' => 'payment status',
            'payment_reference' => 'payment reference',
            'paid_at' => 'payment date',
            'shipping_address' => 'shipping address',
            'minimum_wholesale_quantity' => 'minimum wholesale quantity',
            'quantity_change' => 'stock movement quantity',
            'remaining_stock' => 'remaining stock',
            'period_start' => 'period start date',
            'period_end' => 'period end date',
            'gross_revenue' => 'gross revenue',
            'total_revenue' => 'total revenue',
            'sales_revenue' => 'sales revenue',
            'cacao_costs' => 'cacao costs',
            'employee_costs' => 'employee costs',
            'operational_expenses' => 'operational expenses',
            'total_expenses' => 'total expenses',
            'net_income' => 'net income',
            'net_profit' => 'net profit',
            'remaining_capital' => 'remaining capital',
            'total_production_cost' => 'total production cost',
            'cost_per_pack' => 'cost per pack',
            'pay_type' => 'pay type',
            'pay_date' => 'pay date',
            'salary_total' => 'salary total',
            'total_amount' => 'total amount',
            'password_confirmation' => 'password confirmation',
            'items' => 'order items',
            'items.*.product_id' => 'order item product',
            'items.*.quantity' => 'order item quantity',
            'items.*.price' => 'order item price',
            'items.*.price_type' => 'order item price type',
            'items.*.sub_total' => 'order item subtotal',
        ];
    }

    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(response()->json([
            'status' => 'error',
            'message' => 'Validation failed.',
            'errors' => $validator->errors(),
        ], 422));
    }

    protected function failedAuthorization(): void
    {
        throw new HttpResponseException(response()->json([
            'status' => 'error',
            'message' => 'Unauthorized access.',
        ], 403));
    }
}
