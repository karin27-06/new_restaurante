<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class ConsultasId extends Controller
{
    public function getUserId()
    {
        // Obtener el ID del usuario autenticado
        $user_id = Auth::user()->id;

        // Buscar el usuario en la tabla 'users' con ese ID
        $user = User::find($user_id);

        if ($user) {
            // Retornar el ID del usuario y su nombre completo
            return response()->json([
                'success' => true,
                'user_id' => $user_id,
                'name' => $user->name,
                'apellidos' => $user->apellidos,
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Usuario no encontrado'
            ]);
        }
    }
}
