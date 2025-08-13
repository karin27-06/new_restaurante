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
    Schema::create('sales', function (Blueprint $table) {
        $table->id();  // ID de la venta
        $table->foreignId('idCustomer')->constrained('customers');
        $table->foreignId('idOrder')->constrained('orders'); 
        $table->string('documentType'); 
        $table->string('paymentType');
        $table->string('operationCode')->nullable();
        $table->string('stateSunat')->nullable();  
        $table->timestamps(); 
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};
