<?php

namespace App\Http\Requests\Sale;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSaleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Cambiar según tus necesidades de autorización
    }

    public function rules(): array
    {
        return [
            'total' => 'required|numeric|min:0',
            'idCustomer' => 'required|exists:customers,id',
            'documentType' => 'required|string|max:100',
            'paymentType' => 'required|string|max:100',
            'operationCode' => 'nullable|string|max:100',
        ];
    }

    public function messages(): array
    {
        return [
            'total.required' => 'El total es obligatorio.',
            'total.numeric' => 'El total debe ser un número.',
            'total.min' => 'El total debe ser mayor o igual a 0.',
            'idCustomer.required' => 'El cliente es obligatorio.',
            'idCustomer.exists' => 'El cliente no existe.',
            'documentType.required' => 'El tipo de documento es obligatorio.',
            'paymentType.required' => 'El tipo de pago es obligatorio.',
            'operationCode.string' => 'El código de operación debe ser una cadena de texto.',
        ];
    }
}
