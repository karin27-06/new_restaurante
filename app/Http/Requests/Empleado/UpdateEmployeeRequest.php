<?php

namespace App\Http\Requests\Empleado;
use Illuminate\Foundation\Http\FormRequest;

class UpdateEmployeeRequest extends FormRequest{
    public function authorize(): bool{
        return true;
    }
    public function rules(): array{
        return [
            'name' => 'required|string|max:150',
            'codigo' => 'required|string|max:8|unique:employees,codigo,' . $this->route('employee')->id,
            'employee_type_id' => 'required|exists:employee_types,id',
            'state' => 'required|boolean',
        ];
    }
    public function messages(): array{
        return [
            'name.required' => 'El nombre es obligatorio.',
            'name.string' => 'El nombre debe ser una cadena de texto.',
            'name.max' => 'El nombre no debe exceder los 150 caracteres.',

            'codigo.required' => 'El código es obligatorio.',
            'codigo.string' => 'El código debe ser una cadena de texto.',
            'codigo.max' => 'El código no debe exceder los 8 caracteres.',
            'codigo.unique' => 'El código ya está en uso.',

            'employee_type_id.required' => 'El tipo de empleado es obligatorio.',
            'employee_type_id.exists' => 'El tipo de empleado seleccionado no es válido.',

            'state.required' => 'El estado es obligatorio.',
            'state.boolean' => 'El estado debe ser verdadero o falso',
        ];
    }
}
