<?php

namespace App\Http\Requests\Tipo_Cliente;

use Illuminate\Foundation\Http\FormRequest;

class StoreClientTypeRequest extends FormRequest{
    public function authorize(): bool{
        return true;
    }
    public function prepareForValidation(): void{
        $this->merge([
            'name' => strtolower($this->input('name')),
        ]);
    }
    public function rules(): array{
        return [
            'name' => 'required|string|max:100',
            'state' => 'required|boolean',
        ];
    }
    public function messages(): array{
        return [
            'name.required' => 'El nombre es obligatorio.',
            'name.string' => 'El nombre debe ser texto.',
            'name.max' => 'El nombre no debe exceder los 100 caracteres.',

            'state.required' => 'El estado es obligatorio.',
            'state.boolean' => 'El estado debe ser verdadero o falso.',
        ];
    }
}
