<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\DB;

class MovementInputResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        // Obtener el estado del IGV desde la tabla movements_inputs
        $movementInput = DB::table('movementsInput')->where('id', $this->id)->first();
        $igv_state = $movementInput->igv_state ?? 0;  // Por si no existe el valor, asignamos 0 por defecto

        // Obtener los detalles de los movimientos
        $movementDetails = DB::table('detail_movements_inputs')
            ->where('idMovementInput', $this->id)
            ->get();

        // Inicializar las variables
        $total = 0;
        $totalIGV = 0;
        $subtotal = 0;

        // Recorrer los detalles para hacer los cálculos
        foreach ($movementDetails as $detail) {
            if ($igv_state == 0) {
                 // El totalPrice ya tiene el IGV incluido.
                // El IGV es el 18% del total (que es el precio con el IGV)
                $igv = $detail->totalPrice * 0.18;

                // El subtotal es el totalPrice menos el IGV
                $subtotal += $detail->totalPrice - $igv;

                // El totalIGV es simplemente el IGV calculado
                $totalIGV += $igv;
            } else if ($igv_state == 1) {
               $igv = $detail->totalPrice * 0.18;
                $totalIGV += $igv;
                
                // El subtotal es el totalPrice menos el IGV
                $subtotal += $detail->totalPrice;
            }

            // Acumulando el total de los detalles
        $total = $subtotal + $totalIGV;
        }

        return [
            'id' => $this->id,
            'code' => $this->code,
            'issue_date' => Carbon::parse($this->issue_date)->format('d-m-Y'),
            'execution_date' => Carbon::parse($this->execution_date)->format('d-m-Y'),
            'supplier_id' => $this->supplier_id,
            'supplier_name' => $this->supplier ? $this->supplier->name : null,
            'user_id' => $this->user_id,
            'user_name' => $this->user ? $this->user->name : null,
            'movement_type' => $this->movement_type,
            'state' => $this->state,
            'igv_state' => $this->igv_state,
            'payment_type' => $this->payment_type,
            'created_at' => Carbon::parse($this->created_at)->format('d-m-Y H:i:s A'),
            'updated_at' => Carbon::parse($this->updated_at)->format('d-m-Y H:i:s A'),
            'total' => 'S/.' . number_format($total, 2, '.', ','),  // Formateo en soles
            'igv' => 'S/.' . number_format($totalIGV, 2, '.', ','),  // Formateo en soles
            'sub' => 'S/.' . number_format($subtotal, 2, '.', ','),  // Formateo en soles
        ];
    }
}
