<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVendorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vendors', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->boolean('verified')->default(false);
            $table->longText('bio')->nullable();
            $table->string('facebook')->nullable();
            $table->string('address');
            $table->string('instagram')->nullable();
            $table->string('twitter')->nullable();
            $table->string('phone');
            $table->string('branch')->nullable();
            $table->string('lat');
            $table->string('lng');
            $table->string('place_id')->nullable();
            $table->string('image')->nullable();
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
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
        Schema::dropIfExists('vendors');
    }
}
