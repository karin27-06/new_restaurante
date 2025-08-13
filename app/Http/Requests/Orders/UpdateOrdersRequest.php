<?php

namespace App\Http\Requests\Orders;

use Illuminate\Foundation\Http\FormRequest;

class UpdateOrdersRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Asegúrate de que el usuario tenga permisos para actualizar la orden
        return true;  // Aquí puedes agregar una lógica de autorización si es necesario
    }

    public function prepareForValidation(): void
    {
        // Si necesitas modificar algún valor antes de la validación, puedes hacerlo aquí
        // Por ejemplo, convertir algún campo a mayúsculas, o ajustar valores
        $this->merge([
            'state' => strtolower($this->input('state')),  // Convertir el estado a minúsculas
        ]);
    }

    public function rules()
    {
        return [
            'state' => 'required|in:pendiente,finalizado',  // Validar que el estado sea 'pendiente' o 'finalizado'
        ];
    }

    // Mensajes personalizados de validación
    public function messages()
    {
        return [
            'state.required' => 'El estado del pedido es obligatorio.',
            'state.in' => 'El estado debe ser uno de los siguientes: pendiente, finalizado.',
        ];
    }
}
