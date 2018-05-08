<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEventsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('events', function (Blueprint $table) {
            $table->increments('id');
            $table->string('aggregate', 128)->index();
            $table->string('uuid', 36)->index();
            $table->string('type', 128)->index();
            $table->json('payload')->nullable();
            $table->timestamp('timestamp')->useCurrent()->index();
        });
    }
}
