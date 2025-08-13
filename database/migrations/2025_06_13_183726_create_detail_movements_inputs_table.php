<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDetailMovementsInputsTable extends Migration
{
    public function up()
    {
        Schema::create('detail_movements_inputs', function (Blueprint $table) {
            $table->id(); // id
            $table->foreignId('idMovementInput')->nullable()->constrained('movementsInput'); // Puede ser nulo
            $table->foreignId('idInput')->constrained('inputs'); // No puede ser nulo
            $table->decimal('quantity', 10, 2); // No puede ser nulo
            $table->decimal('totalPrice', 10, 2)->nullable(); // Puede ser nulo
            $table->decimal('priceUnit', 10, 2)->nullable(); // Puede ser nulo
            $table->string('batch')->nullable(); // Puede ser nulo
            $table->date('expirationDate')->nullable(); // Puede ser nulo
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('detail_movements_inputs');
    }
}
