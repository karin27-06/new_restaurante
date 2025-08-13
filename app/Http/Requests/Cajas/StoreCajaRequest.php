<?php

namespace App\Http\Requests\Cajas;

use Illuminate\Foundation\Http\FormRequest;

class StoreCajaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'numero_cajas' => ['required', 'integer', 'min:1'],
        ];
    }

    public function messages(): array
    {
        return [
            'numero_cajas.required' => 'El número de cajas es obligatorio.',
            'numero_cajas.min' => 'Debes crear al menos una caja.',
        ];
    }
}
