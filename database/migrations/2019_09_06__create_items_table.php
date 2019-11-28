<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('items', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('image');
            $table->string('description');
            $table->string('category_name');
            $table->string('generic');
            $table->unsignedBigInteger('reviews_count')->default(0);
            $table->unsignedBigInteger('avg_rating')->default(0);
            $table->unsignedInteger('sold')->default(0);
            $table->unsignedInteger('promo')->default(0);
            $table->unsignedInteger('price')->default(0);
            $table->unsignedBigInteger('category_id');
            $table->unsignedBigInteger('vendor_id');
            $table->string('vendor_name');
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade')->onUpdate('cascade');

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
        Schema::dropIfExists('items');
    }
}
