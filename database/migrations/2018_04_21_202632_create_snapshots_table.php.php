<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSnapshotsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('snapshots', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('event_id')->unsigned();
            $table->string('aggregate', 128)->index();
            $table->string('uuid', 36)->index();
            $table->json('payload')->nullable();
            $table->timestamp('timestamp')->useCurrent()->index();

            $table->foreign('event_id')
                ->references('id')
                ->on('events')
                ->onDelete('restrict');
        });
    }
}
