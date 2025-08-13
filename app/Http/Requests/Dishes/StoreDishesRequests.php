<?php

namespace App\Http\Requests\Dishes;

use Illuminate\Foundation\Http\FormRequest;

class StoreDishesRequests extends FormRequest
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
            'price' => 'required|numeric|min:1|max:999999.99',
            'quantity' => 'required|integer|min:1|max:1000000',
            'idCategory' => 'required|exists:categories,id',
            'state' => 'required|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'El nombre es obligatorio.',
            'name.string' => 'El nombre debe ser texto.',
            'name.max' => 'El nombre no debe exceder los 100 caracteres.',

            'price.required' => 'El precio es obligatorio.',
            'price.numeric' => 'El precio debe ser un número.',
            'price.min' => 'El precio no puede ser negativo.',
            'price.max' => 'El precio no puede exceder 999999.99.',

            'quantity.required' => 'La cantidad es obligatoria.',
            'quantity.integer' => 'La cantidad debe ser un número entero.',
            'quantity.min' => 'La cantidad no puede ser negativa.',
            'quantity.max' => 'La cantidad no puede exceder 1,000,000.',

            'idCategory.required' => 'La categoría es obligatoria.',
            'idCategory.exists' => 'La categoría seleccionada no existe.',

            'state.required' => 'El estado es obligatorio.',
            'state.boolean' => 'El estado debe ser verdadero o falso.',
        ];
    }
}
