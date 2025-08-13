<?php

namespace App\Http\Requests\Inputs;

use Illuminate\Foundation\Http\FormRequest;

class StoreInputRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function prepareForValidation(): void
    {
        $this->merge([
            'name' => strtolower($this->input('name')),
        ]);
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:100',
            'priceSale' => 'required|numeric|min:0',
            'priceBuy' => 'nullable|numeric|min:0',
            'quantityUnitMeasure' => 'nullable|numeric|min:0',
            'idAlmacen' => 'required|exists:almacens,id',
            'description' => 'nullable|string',
            'state' => 'required|boolean',
            'unitMeasure' => 'required|string|max:100',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'El nombre es obligatorio.',
            'name.string' => 'El nombre debe ser texto.',
            'name.max' => 'El nombre no debe exceder los 100 caracteres.',

            'priceSale.required' => 'El precio es obligatorio.',
            'priceSale.numeric' => 'El precio debe ser un número.',
            'priceSale.min' => 'El precio no puede ser negativo.',
                
            'quantityUnitMeasure.required' => 'La cantidad de medida es obligatorio.',
            'quantityUnitMeasure.numeric' => 'La cantidad de medida  debe ser un número.',
            'quantityUnitMeasure.min' => 'La cantidad de medida  no puede ser negativo.',

            'idAlmacen.required' => 'El almacén es obligatorio.',
            'idAlmacen.exists' => 'El almacén seleccionado no existe.',

            'description.string' => 'La descripción debe ser texto.',

            'state.required' => 'El estado es obligatorio.',
            'state.boolean' => 'El estado debe ser verdadero o falso.',
        ];
    }
}
