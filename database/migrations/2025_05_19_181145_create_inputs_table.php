<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInputsTable extends Migration
{
    public function up()
    {
        Schema::create('inputs', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->decimal('priceBuy', 10, 2)->nullable();
            $table->decimal('priceSale', 10, 2);
            $table->decimal('quantityUnitMeasure', 10, 2);
            $table->foreignId('idAlmacen')->constrained('almacens')->onDelete('cascade');
            $table->text('description')->nullable();
            $table->string(column: 'unitMeasure');
            $table->boolean('state')->default(true);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('inputs');
    }
}
