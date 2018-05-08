<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCarriersTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('carriers', function (Blueprint $table) {
            $table->increments('id');
            $table->string('uuid', 36)->index();
            $table->string('usdot', 8)->index();
            $table->string('name', 64)->index();
            $table->integer('drivers')->unsigned()->default(0)->index();
            $table->integer('trucks')->unsigned()->default(0)->index();
            $table->boolean('interstate')->default(false)->index();
            $table->boolean('active')->default(false)->index();
            $table->timestamps();
        });
    }
}
