<?php

namespace App\Http\Requests\MovementInputDetails;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMovementInputDetailRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

public function rules(): array
{
    return [
        'idMovementInput' => 'required|integer',  // Solo aseguramos que sea un número entero
        'idInput' => 'required|exists:inputs,id',  // Asegura que el insumo exista
        'quantity' => 'required|numeric|min:0',    // Asegura que la cantidad sea un número positivo
        'totalPrice' => 'required|numeric|min:0',  // Precio total no negativo
        'priceUnit' => 'required|numeric|min:0',   // Precio unitario no negativo
        'batch' => 'required|string|max:100',      // Lote del insumo
        'expirationDate' => 'required|date|after_or_equal:today', // Fecha de vencimiento no en el pasado
    ];
}


    public function messages(): array
    {
        return [
            'idMovementInput.required' => 'El movimiento de insumo es obligatorio.',
            'idMovementInput.exists' => 'El movimiento de insumo seleccionado no existe.',
            'idInput.required' => 'El insumo es obligatorio.',
            'idInput.exists' => 'El insumo seleccionado no existe.',
            'quantity.required' => 'La cantidad es obligatoria.',
            'quantity.numeric' => 'La cantidad debe ser un número.',
            'quantity.min' => 'La cantidad no puede ser negativa.',

            'totalPrice.required' => 'El precio total es obligatorio.',
            'totalPrice.numeric' => 'El precio total debe ser un número.',
            'totalPrice.min' => 'El precio total no puede ser negativo.',
            'priceUnit.required' => 'El precio unitario es obligatorio.',
            'priceUnit.numeric' => 'El precio unitario debe ser un número.',
            'priceUnit.min' => 'El precio unitario no puede ser negativo.',
            'batch.required' => 'El lote es obligatorio.',
            'batch.string' => 'El lote debe ser una cadena de texto.',
            'batch.max' => 'El lote no puede tener más de 100 caracteres.',
            'expirationDate.required' => 'La fecha de vencimiento es obligatoria.',
            'expirationDate.date' => 'La fecha de vencimiento debe ser una fecha válida.',
            'expirationDate.after_or_equal' => 'La fecha de vencimiento no puede ser una fecha pasada.',
        ];
    }
}
