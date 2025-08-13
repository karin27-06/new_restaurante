<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CertificadoController extends Controller
{
    public function subircertificado(Request $request)
    {
        \Log::info('🔄 Iniciando subida de certificado...');

        $request->validate([
            'certificado' => 'required|file|mimetypes:text/plain,application/x-pem-file',
        ]);

        try {
            $contenido = file_get_contents($request->file('certificado')->getRealPath());
            \Log::info('✅ Contenido del certificado obtenido correctamente.');

            // Guardar el archivo en storage/app/sunat/certificados/certificate.pem
            $path = 'sunat/certificados/certificate.pem';
            Storage::put($path, $contenido);

            // Verificar si se guardó correctamente
            if (Storage::exists($path)) {
                \Log::info('✅ Certificado guardado exitosamente en: ' . storage_path('app/' . $path));
                return response()->json([
                    'message' => 'Certificado subido correctamente.'
                ]);
            } else {
                \Log::warning('⚠️ Certificado no se guardó en storage.');
                return response()->json([
                    'message' => 'El certificado no se pudo guardar correctamente.'
                ], 500);
            }

        } catch (\Exception $e) {
            \Log::error('❌ Error al subir el certificado: ' . $e->getMessage());
            return response()->json([
                'message' => 'Error al subir el certificado.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
