<?php

namespace App\Http\Requests\MovementInputs;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMovementInputRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function prepareForValidation(): void
    {
        $this->merge([
            'code' => strtoupper($this->input('code')),  // Convertimos el código a mayúsculas
        ]);
    }

    public function rules(): array
    {
        return [
        'code' => 'required|string|max:50|unique:movementsInput,code,' . $this->route('movementInput')->id,  // Validamos que el código sea único
         'issue_date' => 'required|date',  // Validamos que sea una fecha válida
        'execution_date' => 'required|date|after_or_equal:issue_date',  // Validamos que la fecha de ejecución sea igual o posterior a la de emisión
        'supplier_id' => 'required|exists:suppliers,id',  // Validamos que el proveedor exista en la tabla de clientes
        'user_id' => 'required|exists:users,id',  // Validamos que el usuario exista en la tabla de usuarios
        'movement_type' => 'required|in:1,2,3',  // Validamos que el tipo de movimiento sea 1 (FACTURA), 2 (GUIA), o 3 (BOLETA)
        'state' => 'required|boolean',  // Validamos que el estado sea booleano
        'igv_state' => 'required|in:0,1',  // Validamos que el estado de IGV sea 0 (INCLUYE) o 1 (NO INCLUYE)
        'payment_type' => 'required|in:contado,credito',  // Validamos que el tipo de pago sea contado o crédito
    ];
    }

    public function messages(): array
    {
        return [
            'code.required' => 'El código es obligatorio.',
            'code.string' => 'El código debe ser una cadena de texto.',
            'code.max' => 'El código no puede exceder los 50 caracteres.',
            'code.unique' => 'Este código ya está registrado.',

            'issue_date.required' => 'La fecha de emisión es obligatoria.',
            'issue_date.date' => 'La fecha de emisión debe ser una fecha válida.',

            'execution_date.required' => 'La fecha de ejecución es obligatoria.',
            'execution_date.date' => 'La fecha de ejecución debe ser una fecha válida.',
            'execution_date.after_or_equal' => 'La fecha de ejecución no puede ser anterior a la fecha de emisión.',

            'supplier_id.required' => 'El proveedor es obligatorio.',
            'supplier_id.exists' => 'El proveedor seleccionado no existe.',

            'user_id.required' => 'El usuario es obligatorio.',
            'user_id.exists' => 'El usuario seleccionado no existe.',

            'movement_type.required' => 'El tipo de movimiento es obligatorio.',
            'movement_type.in' => 'Debes seleccionar un tipo de documento',

            'state.required' => 'El estado es obligatorio.',
            'state.boolean' => 'El estado debe ser verdadero o falso.',

            'igv_state.required' => 'El estado del IGV es obligatorio.',
            'igv_state.boolean' => 'El estado del IGV debe ser verdadero o falso.',

            'payment_type.required' => 'El tipo de pago es obligatorio.',
            'payment_type.in' => 'El tipo de pago debe ser contado o crédito.',
        ];
    }
}
