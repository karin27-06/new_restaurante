<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up(): void
    {
        Schema::create('reporte_caja', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_caja');
            $table->unsignedBigInteger('idUsuario');
            $table->unsignedBigInteger('idVendedor')->nullable();
            $table->decimal('monto_efectivo', 10, 2)->nullable();
            $table->decimal('monto_tarjeta', 10, 2)->nullable();
            $table->decimal('monto_yape', 10, 2)->nullable();
            $table->decimal('monto_transferencia', 10, 2)->nullable();
            $table->timestamp('fecha_ingreso')->nullable(); // Fecha de apertura
            $table->timestamp('fecha_salida')->nullable(); // Fecha de cierre
            $table->boolean('estado_caja')->default(false); // Ocupada por defecto
            $table->timestamps();
            
            $table->foreign('id_caja')->references('id')->on('cajas')->onDelete('cascade');
            $table->foreign('idUsuario')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('idVendedor')->references('id')->on('users')->onDelete('cascade');
        });
    }//

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reporte_caja');
    }
};
