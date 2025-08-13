<?php
namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MovementInputKardexResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        // Obtener la cantidad usando el método getQuantity()
        $quantity = $this->getQuantity();  // Usamos el método definido en el modelo KardexInput

        return [
            'id' => $this->id,  // ID del Kardex
            'idUser' => $this->idUser,  // ID del usuario
            'username' => $this->user ? strtoupper($this->user->username) : null,  // Nombre de usuario en mayúsculas
            'idInput' => $this->idInput,  // ID del insumo
            'nameInput'=>$this->input->name,
            'quantityUnitMeasure' => $this->input->quantityUnitMeasure,
            'unitMeasure' => $this->input->unitMeasure,
            'code' => $this->movementInput->code?? null,
            'idMovementInput' => $this->idMovementInput ?? null,  // `idMovementInput` ahora puede ser null
            'quantity' => $quantity,  // Cantidad obtenida desde detail_movements_inputs
            'movement_type' => $this->movement_type == 0 ? 'INGRESO' : 'SALIDA',
            'totalPrice' => $this->totalPrice,  // Precio total
            'created_at' => Carbon::parse($this->created_at)->format('d-m-Y H:i:s A'),  // Fecha de creación
            'updated_at' => Carbon::parse($this->updated_at)->format('d-m-Y H:i:s A'),  // Fecha de actualización
        ];
    }
}
