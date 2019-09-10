<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFoodsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('foods', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('description');
            $table->string('category');
            $table->string('image');
            $table->string('generic');
            $table->unsignedBigInteger('vendor_id');
            $table->unsignedBigInteger('reviews_count')->default(0);
            $table->unsignedBigInteger('avg_rating')->default(0);
            $table->unsignedInteger('total_qty')->default(0);
            $table->boolean('available')->default(false);
            $table->unsignedInteger('available_qty')->default(0);
            $table->unsignedInteger('price')->default(0);
            $table->foreign('vendor_id')->references('id')->on('vendors');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('foods');
    }
}
