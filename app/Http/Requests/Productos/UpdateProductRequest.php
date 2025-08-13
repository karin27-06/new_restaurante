<?php

namespace App\Http\Requests\Productos;
use Illuminate\Foundation\Http\FormRequest;
class UpdateProductRequest extends FormRequest{
    public function authorize(): bool{
        return true;
    }
    public function rules(): array{
        return [
            'name' => ['required', 'string', 'min:2', 'max:100'],
            'idCategory' => ['required', 'exists:categories,id'],
            'details' => ['nullable', 'string', 'max:500'],
            'idAlmacen' => ['required', 'exists:almacens,id'],
            'state' => ['required', 'boolean'],
        ];
    }
    public function messages(): array{
        return [
            'name.required' => 'El nombre es obligatorio.',
            'name.min' => 'El nombre debe tener al menos 2 caracteres.',
            'name.max' => 'El nombre no debe superar los 100 caracteres.',
            'idCategory.required' => 'La categoría es obligatoria.',
            'idCategory.exists' => 'La categoría seleccionada no existe.',
            'details.max' => 'Los detalles no deben superar los 500 caracteres.',
            'idAlmacen.required' => 'El almacén es obligatorio.',
            'idAlmacen.exists' => 'El almacén seleccionado no existe.',
            'state.required' => 'El estado es obligatorio.',
            'state.boolean' => 'El estado debe ser verdadero o falso.',
        ];
    }
}
