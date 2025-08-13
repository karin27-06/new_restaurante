<?php

namespace App\Http\Requests\Proveedor;

use Illuminate\Foundation\Http\FormRequest;

class StoreSupplierRequest extends FormRequest{
    public function authorize(): bool{
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:150|unique:suppliers,name',
            'ruc' => ['required', 'digits:11', 'unique:suppliers,ruc'],
            'address' => 'required|string|max:255',
            'phone' => ['required', 'regex:/^[0-9]{7,11}$/'], // rango de 7 a 9 digitos
            'state' => 'required|boolean',
        ];
    }

    public function messages(): array{
        return [
            'name.required' => 'El nombre es obligatorio.',
            'name.string' => 'El nombre debe ser una cadena de texto.',
            'name.max' => 'El nombre no debe exceder los 150 caracteres.',
            'name.unique' => 'El nombre ya está en uso.',

            'ruc.required' => 'El RUC es obligatorio.',
            'ruc.digits' => 'El RUC debe contener exactamente 11 números.',
            'ruc.unique' => 'El RUC ya está en uso.',

            'address.required' => 'La dirección es obligatoria.',
            'address.string' => 'La dirección debe ser una cadena de texto.',
            'address.max' => 'La dirección no debe exceder los 255 caracteres.',

            'phone.required' => 'El teléfono es obligatorio.',
            'phone.regex' => 'El teléfono debe contener solo números y tener un rango de 9 digitos.',

            'state.required' => 'El estado es obligatorio.',
            'state.boolean' => 'El estado debe ser verdadero o falso.',
        ];
    }
}
