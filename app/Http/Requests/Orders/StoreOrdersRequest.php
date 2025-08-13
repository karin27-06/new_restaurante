<?php

namespace App\Http\Requests\Orders;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrdersRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Ajusta según tu política
    }

    public function rules(): array
    {
        return [
            'idCustomer'=>'required|exists:customers,id',
            'idUser' => 'required|exists:users,id',
            'idTable' => 'required|exists:tables,id', // Asegura que la mesa exista
            'state'=>'string|max:255',

            // VALIDACIÓN DE PLATOS
            'platos' => 'required|array|min:1',
            'platos.*.id' => 'required|exists:dishes,id',
            'platos.*.cantidad' => 'required|integer|min:1',
            'platos.*.price' => 'required|numeric|min:0',
        ];
    }
}