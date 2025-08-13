<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMovementsInputsTable extends Migration
{
  public function up(): void
{
    Schema::create('movementsInput', function (Blueprint $table) {
        $table->id(); // id
        $table->string('code'); // El código es un string, puede ser número de serie de una boleta, factura o guía
        $table->date('issue_date'); // Fecha de emisión
        $table->date('execution_date'); // Fecha de ejecución
        $table->foreignId('supplier_id')->constrained('suppliers', 'id'); // Relación con la tabla 'Suppliers'
        $table->foreignId('user_id')->constrained('users', 'id'); // Relación con la tabla 'users'
        $table->integer('movement_type'); // El tipo de movimiento (1: FACTURA, 2: GUIA, 3: BOLETA)
        $table->boolean('state')->default(true); // Estado (true o false)
        $table->tinyInteger('igv_state')->default(0); // Estado del IGV (0: INCLUYE, 1: NO INCLUYE)
        $table->enum('payment_type', ['contado', 'credito']); // Tipo de pago (contado o crédito)
        $table->timestamps(); // Timestamps para created_at y updated_at
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('movementsInput');
    }
}
