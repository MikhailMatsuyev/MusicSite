<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAlbumsTable extends Migration
{
    public function up()
    {
        Schema::create('albums', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('photo')->nullable();
            $table->integer('year')->unsigned();
            $table->foreign('artist_id')->references('id')->on('artists')->onDelete('cascade');
            $table->integer('artist_id')->unsigned()->default(0);
            $table->timestamps();
        });
    }
    public function down()
    {
        Schema::drop('albums');
    }
}
