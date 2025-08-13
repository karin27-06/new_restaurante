<?php

namespace App\Http\Requests\KardexInput;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMovementInputKardexRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function prepareForValidation(): void
    {
        $this->merge([
            'code' => strtoupper($this->input('code')),  // Convertir el código a mayúsculas
        ]);
    }

    public function rules()
    {
        return [
            'idInput' => 'required|integer',
            'idMovementInput' => 'required|integer',
            'totalPrice' => 'required|numeric|min:0',
        ];
    }

    // Mensajes personalizados de validación
    public function messages()
    {
        return [
            'idInput.required' => 'El ID del insumo es obligatorio.',
            'idInput.integer' => 'El ID del insumo debe ser un número entero.',
            'idMovementInput.required' => 'El IdMovementInput del insumo es obligatorio.',
            'idMovementInput.integer' => 'El IdMovementInput del insumo debe ser un número entero.',
            'totalPrice.required' => 'El precio total es obligatorio.',
            'totalPrice.numeric' => 'El precio total debe ser un número.',
            'totalPrice.min' => 'El precio total debe ser mayor o igual a 0.',
        ];
    }
}
