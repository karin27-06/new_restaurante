<?php

namespace App\Http\Requests\Presentacion;

use Illuminate\Foundation\Http\FormRequest;

class StorePresentationRequest extends FormRequest{
    public function authorize(): bool{
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:150|unique:presentations,name',
            'description' => 'nullable|string|max:255',
            'state' => 'required|boolean',
        ];
    }

    public function messages(): array{
        return [
            'name.required' => 'El nombre es obligatorio.',
            'name.string' => 'El nombre debe ser una cadena de texto.',
            'name.max' => 'El nombre no debe exceder los 150 caracteres.',
            'name.unique' => 'El nombre ya está en uso.',

            'description.string' => 'La descripción debe ser una cadena de texto.',
            'description.max' => 'La descripción no debe exceder los 255 caracteres.',

            'state.required' => 'El estado es obligatorio.',
            'state.boolean' => 'El estado debe ser verdadero o falso.',
        ];
    }
}
