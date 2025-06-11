<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVehicleTypesTable extends Migration
{
    public function up()
    {
        Schema::create('vehicle_types', function (Blueprint $table) {
            $table->increments('id'); // INT UNSIGNED PRIMARY KEY
            $table->text('description_vehicle');
            $table->decimal('price', 10, 2);
        });
    }

    public function down()
    {
        Schema::dropIfExists('vehicle_types');
    }
}