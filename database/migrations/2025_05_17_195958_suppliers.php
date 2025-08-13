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
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('ruc', 11)->unique()->comment('RUC de 11 dígitos numéricos');
            $table->string('address');
            $table->string('phone', 11)->comment('Telefono de 9 dígitos numéricos');;
            $table->boolean('state')->default(true)->comment('0: Inactivo, 1: Activo');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('suppliers');
    }
};
