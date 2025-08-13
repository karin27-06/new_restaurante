<?php

namespace App\Http\Requests\Sale;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreSaleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Cambiar según tus necesidades de autorización
    }

    public function rules(): array
    {
        return [
            'idCustomer' => 'required|exists:customers,id',
            'idOrder' => [
                'required',
                'exists:orders,id',
                Rule::unique('sales', 'idOrder') // Asegura que el idOrder no se repita en la tabla de ventas
            ],
            'documentType' => 'required|string|max:100',
            'paymentType' => 'required|string|max:100',
            'operationCode' => 'nullable|string|max:100',
        ];
    }

    public function messages(): array
    {
        return [
            'idCustomer.required' => 'El cliente es obligatorio.',
            'idCustomer.exists' => 'El cliente no existe.',
            'idOrder.required' => 'La Orden es obligatorio.',
            'idOrder.exists' => 'La orden no existe.',
            'idOrder.unique' => 'La venta ya ha sido registrada anteriormente.', // Mensaje personalizado para la validación de unicidad
            'documentType.required' => 'El tipo de documento es obligatorio.',
            'paymentType.required' => 'El tipo de pago es obligatorio.',
            'operationCode.string' => 'El código de operación debe ser una cadena de texto.',
        ];
    }
}
