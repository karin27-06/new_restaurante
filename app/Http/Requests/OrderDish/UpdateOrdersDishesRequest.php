<?php

namespace App\Http\Requests\OrderDish;  // Asegúrate de que el namespace sea correcto

use Illuminate\Foundation\Http\FormRequest;

class UpdateOrdersDishesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'state' => 'required|in:pendiente,servido,cancelado,en preparación,en entrega,completado',
        ];
    }

    public function messages(): array
    {
        return [
            'state.required' => 'El estado es obligatorio.',
            'state.in' => 'El estado debe ser uno de los siguientes: pendiente, servido, cancelado, en preparación, en entrega, completado.',
        ];
    }
}
