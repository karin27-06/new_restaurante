<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateKardexInputsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
{
    Schema::create('kardex_inputs', function (Blueprint $table) {
        $table->id(); // Auto-incrementing ID column
        $table->unsignedBigInteger('idUser');
        $table->unsignedBigInteger('idInput'); 
        $table->unsignedBigInteger('idMovementInput')->nullable(); // Aceptar valores nulos
        $table->integer('movement_type'); // El tipo de movimiento (1: FACTURA, 2: GUIA, 3: BOLETA)
        $table->decimal('totalPrice', 10, 2)->nullable(); // Aceptar valores nulos
        $table->timestamps(); 
    });
}


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('kardex_inputs');
    }


    
}
