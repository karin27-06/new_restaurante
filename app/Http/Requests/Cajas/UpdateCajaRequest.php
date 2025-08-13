<?php

namespace App\Http\Requests\Cajas;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCajaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            //'numero_cajas' => 'required|integer|min:1',
            'state' => ['required', 'boolean'],
            'idVendedor' => ['nullable', 'exists:users,id'], // Excluir al usuario autenticado o le cambiamos por idVendedor
        ];
    }

    public function messages(): array
    {
        return [
            'state.required' => 'El estado es obligatorio.',
            'idVendedor.exists' => 'El vendedor seleccionado no existe.',
        ];
    }
}
