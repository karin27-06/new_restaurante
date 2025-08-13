<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('sales_invoices', function (Blueprint $table) {
            $table->id();  // ID de la tabla
            $table->foreignId('idSale')->constrained('sales');  // Relación con la tabla sales
            $table->string('prefix');  // Prefijo (Factura o Boleta)
            $table->string('serie');  // Número de serie
            $table->integer('number');  // Número de la factura o boleta
            $table->timestamps();  // Fecha de creación y actualización
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales_invoices');
    }
};
