<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDishInputTable extends Migration
{
    public function up()
    {
        Schema::create('dish_input', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('dish_id');
            $table->unsignedBigInteger('input_id');

            $table->foreign('dish_id')->references('id')->on('dishes')->onDelete('cascade');
            $table->foreign('input_id')->references('id')->on('inputs')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('dish_input');
    }
}
