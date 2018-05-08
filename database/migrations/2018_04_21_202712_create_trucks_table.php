<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrucksTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('trucks', function (Blueprint $table) {
            $table->increments('id');
            $table->string('uuid', 36)->index();
            $table->string('vin', 17)->index();
            $table->string('make', 32)->index();
            $table->string('model', 32)->index();
            $table->string('year', 4)->index();
            $table->integer('unit')->unsigned()->nullable()->index();
            $table->string('color', 32)->nullable()->index();
            $table->string('lpn', 16)->nullable()->index();
            $table->string('region', 5)->nullable()->index();
            $table->timestamp('expires_at')->nullable()->index();
            $table->timestamps();
        });
    }
}
