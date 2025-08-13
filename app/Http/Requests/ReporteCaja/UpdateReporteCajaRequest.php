<?php

namespace App\Http\Requests\ReporteCaja;

use Illuminate\Foundation\Http\FormRequest;

class UpdateReporteCajaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'monto_efectivo' => ['nullable', 'numeric', 'min:0'],
            'monto_tarjeta' => ['nullable', 'numeric', 'min:0'],
            'monto_yape' => ['nullable', 'numeric', 'min:0'],
            'monto_transferencia' => ['nullable', 'numeric', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'monto_efectivo.numeric' => 'El monto en efectivo debe ser un número válido.',
            'monto_tarjeta.numeric' => 'El monto de la tarjeta debe ser un número válido.',
            'monto_yape.numeric' => 'El monto en Yape/Plin debe ser un número válido.',
            'monto_transferencia.numeric' => 'El monto de la transferencia debe ser un número válido.',
            'monto_efectivo.min' => 'El monto en efectivo no puede ser negativo.',
            'monto_tarjeta.min' => 'El monto de la tarjeta no puede ser negativo.',
            'monto_yape.min' => 'El monto en Yape/Plin no puede ser negativo.',
            'monto_transferencia.min' => 'El monto de la transferencia no puede ser negativo.',
        ];
    }
}
