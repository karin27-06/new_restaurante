<?php

namespace App\Http\Requests\KardexInput;

use Illuminate\Foundation\Http\FormRequest;

class StoreMovementInputKardexRequest extends FormRequest
{
    public function authorize()
    {
        // Permitir la solicitud solo si el usuario tiene autorización
        return true;
    }

  public function rules()
{
    return [
        'idUser' => 'required|integer',
        'idInput' => 'required|integer',
        'idMovementInput' => 'nullable|integer', // Permitir nulo
        'movement_type' => 'required|integer|in:0,1', // 1: FACTURA, 2: GUIA, 3: BOLETA
        'totalPrice' => 'nullable|numeric|min:0', // Permitir nulo
    ];
}

    // Agregar mensajes personalizados para cada regla de validación
    public function messages()
    {
        return [
            'idUser.required' => 'El ID del usuario es obligatorio.',
            'idUser.integer' => 'El ID del usuario debe ser un número entero.',
            
            'idInput.required' => 'El ID del insumo es obligatorio.',
            'idInput.integer' => 'El ID del insumo debe ser un número entero.',
            
            'idMovementInput.required' => 'El ID del movimiento de insumo es obligatorio.',
            'idMovementInput.integer' => 'El ID del movimiento de insumo debe ser un número entero.',
            
            'movement_type.required' => 'El tipo de movimiento es obligatorio.',
            'movement_type.integer' => 'El tipo de movimiento debe ser un número entero.',
            'movement_type.in' => 'El tipo de movimiento debe ser uno de los siguientes: 1 (FACTURA), 2 (GUIA), 3 (BOLETA).',
          
            
            'totalPrice.required' => 'El precio total es obligatorio.',
            'totalPrice.numeric' => 'El precio total debe ser un número.',
            'totalPrice.min' => 'El precio total debe ser mayor o igual a 0.',
        ];
    }
}
