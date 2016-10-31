<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->text('title');
            $table->text('description');
            $table->text('description_long');
            $table->string('slug');
            $table->integer('user_id')->unsigned();
            $table->boolean('active');
            $table->text('settings')->nullable();
            $table->text('thumb')->nullable();
            $table->dateTime('published_at');
            $table->timestamps();


            $table->index(['slug', 'active']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('products');
    }
}
