<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFleetsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('fleets', function (Blueprint $table) {
            $table->increments('id');
            $table->string('truck_uuid', 36);
            $table->string('carrier_uuid', 36);
            $table->string('vin', 17)->index();
            $table->string('make', 32)->index();
            $table->string('model', 32)->index();
            $table->string('year', 4)->index();
            $table->integer('unit')->unsigned()->nullable()->index();
            $table->string('color', 32)->nullable()->index();
            $table->string('lpn', 16)->nullable()->index();
            $table->string('region', 5)->nullable()->index();
            $table->string('usdot', 8)->index();
            $table->string('name', 64)->index();
            $table->integer('drivers')->unsigned()->default(0)->index();
            $table->integer('trucks')->unsigned()->default(0)->index();
            $table->boolean('interstate')->default(false)->index();
            $table->boolean('active')->default(false)->index();
            $table->timestamp('expires_at')->nullable()->index();
            $table->timestamps();

            $table->foreign('truck_uuid')
                ->references('uuid')
                ->on('trucks')
                ->onDelete('restrict');

            $table->foreign('carrier_uuid')
                ->references('uuid')
                ->on('carriers')
                ->onDelete('restrict');
        });
    }
}
