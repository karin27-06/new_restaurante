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
    Schema::create('sales_orders', function (Blueprint $table) {
        $table->id();  // ID del registro
        $table->foreignId('idSale')->constrained('sales');  // Relación con la tabla sales
        $table->foreignId('idOrder')->constrained('orders');  // Relación con la tabla orders
        $table->decimal('subtotal', 10, 2);  // Subtotal de la venta
        $table->timestamps();  // Fecha de creación y actualización
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales_orders');
    }
};
