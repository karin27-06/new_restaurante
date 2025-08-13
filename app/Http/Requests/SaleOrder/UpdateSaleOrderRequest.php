<?php

namespace App\Http\Requests\SaleOrder;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSaleOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function prepareForValidation(): void
    {
        $this->merge([
            'subtotal' => number_format($this->input('subtotal'), 2, '.', ''), // Aseguramos que el subtotal tenga el formato adecuado
        ]);
    }

    public function rules(): array
    {
        return [
            'idSale' => 'required|exists:sales,id', // Verifica que el idSale exista en la tabla sales
            'idOrder' => 'required|exists:orders,id', // Verifica que el idOrder exista en la tabla orders
            'subtotal' => 'required|numeric|min:0', // El subtotal debe ser un número mayor o igual a 0
        ];
    }

    public function messages(): array
    {
        return [
            'idSale.required' => 'El ID de la venta es obligatorio.',
            'idSale.exists' => 'El ID de la venta no existe en la base de datos.',
            
            'idOrder.required' => 'El ID del pedido es obligatorio.',
            'idOrder.exists' => 'El ID del pedido no existe en la base de datos.',
            
            'subtotal.required' => 'El subtotal es obligatorio.',
            'subtotal.numeric' => 'El subtotal debe ser un número.',
            'subtotal.min' => 'El subtotal no puede ser negativo.',
        ];
    }
}
