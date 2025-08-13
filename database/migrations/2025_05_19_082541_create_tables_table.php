<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTablesTable extends Migration
{
    public function up(): void
    {
        Schema::create('tables', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('tablenum');
            $table->unsignedBigInteger('idArea');
            $table->unsignedBigInteger('idFloor');
            $table->integer('capacity');
            $table->boolean('state')->default(true);
            $table->timestamps();

            // Claves foráneas
            $table->foreign('idArea')->references('id')->on('areas')->onDelete('cascade');
            $table->foreign('idFloor')->references('id')->on('floors')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tables');
    }
}
