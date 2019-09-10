<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBreadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('breads', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('desc');
            $table->string('image');
            $table->string('category');
            $table->string('breadable_type');
            $table->integer('breadable_id');
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
        Schema::dropIfExists('breads');
    }
}
