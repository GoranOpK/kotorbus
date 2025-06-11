<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReservationsTable extends Migration
{
    public function up()
    {
        Schema::create('reservations', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('drop_off_time_slot_id');
            $table->unsignedInteger('pick_up_time_slot_id');
            $table->date('reservation_date');
            $table->string('user_name', 255);
            $table->string('country', 100);
            $table->string('license_plate', 50);
            $table->unsignedInteger('vehicle_type_id');
            $table->string('email', 255);
            $table->enum('status', ['paid', 'pending'])->default('pending');

            $table->foreign('drop_off_time_slot_id')->references('id')->on('list_of_time_slots');
            $table->foreign('pick_up_time_slot_id')->references('id')->on('list_of_time_slots');
            $table->foreign('vehicle_type_id')->references('id')->on('vehicle_types');
        });
    }

    public function down()
    {
        Schema::dropIfExists('reservations');
    }
}