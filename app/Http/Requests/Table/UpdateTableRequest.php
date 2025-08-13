<?php

namespace App\Http\Requests\Table;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTableRequest extends FormRequest
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
            'name'      => 'required|string|max:100',
            'tablenum'  => 'required|string|max:50',
            'idArea'    => 'required|exists:areas,id',
            'idFloor'   => 'required|exists:floors,id',
            'capacity'  => 'required|integer|min:1',
            'state'     => 'required|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'     => 'El nombre es obligatorio.',
            'name.string'       => 'El nombre debe ser una cadena de texto.',
            'name.max'          => 'El nombre no puede exceder los 100 caracteres.',

            'tablenum.required' => 'El número de mesa es obligatorio.',
            'tablenum.string'   => 'El número de mesa debe ser texto.',
            'tablenum.max'      => 'El número de mesa no debe exceder los 50 caracteres.',

            'idArea.required'   => 'El área es obligatoria.',
            'idArea.exists'     => 'El área seleccionada no existe.',

            'idFloor.required'  => 'El piso es obligatorio.',
            'idFloor.exists'    => 'El piso seleccionado no existe.',

            'capacity.required' => 'La capacidad es obligatoria.',
            'capacity.integer'  => 'La capacidad debe ser un número entero.',
            'capacity.min'      => 'La capacidad debe ser al menos 1.',

            'state.required'    => 'El estado es obligatorio.',
            'state.boolean'     => 'El estado debe ser verdadero o falso.',
        ];
    }
}
