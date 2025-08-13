<?php

namespace App\Http\Requests\MovementInputDetails;

use Illuminate\Foundation\Http\FormRequest;

class StoreMovementInputDetailRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'idMovementInput' => 'nullable|exists:movementsInput,id', // Permite que sea nulo
            'idInput' => 'required|exists:inputs,id', // Asegura que el insumo exista y no sea nulo
            'quantity' => 'required|numeric|min:0', // Asegura que la cantidad sea un número positivo y obligatorio
            'totalPrice' => 'nullable|numeric|min:0', // Precio total puede ser nulo
            'priceUnit' => 'nullable|numeric|min:0', // Precio unitario puede ser nulo
            'batch' => 'nullable|string|max:100', // Lote puede ser nulo
            'expirationDate' => 'nullable|date|after_or_equal:today', // Fecha de vencimiento puede ser nula
        ];
    }

    public function messages(): array
    {
        return [
            'idMovementInput.nullable' => 'El movimiento de insumo puede ser nulo.',
            'idMovementInput.exists' => 'El movimiento de insumo seleccionado no existe.',
            'idInput.required' => 'El insumo es obligatorio.',
            'idInput.exists' => 'El insumo seleccionado no existe.',
            'quantity.required' => 'La cantidad es obligatoria.',
            'quantity.numeric' => 'La cantidad debe ser un número.',
            'quantity.min' => 'La cantidad no puede ser negativa.',
            'totalPrice.nullable' => 'El precio total puede ser nulo.',
            'totalPrice.numeric' => 'El precio total debe ser un número.',
            'totalPrice.min' => 'El precio total no puede ser negativo.',
            'priceUnit.nullable' => 'El precio unitario puede ser nulo.',
            'priceUnit.numeric' => 'El precio unitario debe ser un número.',
            'priceUnit.min' => 'El precio unitario no puede ser negativo.',
            'batch.nullable' => 'El lote puede ser nulo.',
            'batch.string' => 'El lote debe ser una cadena de texto.',
            'batch.max' => 'El lote no puede tener más de 100 caracteres.',
            'expirationDate.nullable' => 'La fecha de vencimiento puede ser nula.',
            'expirationDate.date' => 'La fecha de vencimiento debe ser una fecha válida.',
            'expirationDate.after_or_equal' => 'La fecha de vencimiento no puede ser una fecha pasada.',
        ];
    }
}
