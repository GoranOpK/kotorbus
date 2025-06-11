<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MakeTimeSlotIdsNullableInReservationsTable extends Migration
{
    public function up()
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->unsignedInteger('drop_off_time_slot_id')->nullable()->change();
            $table->unsignedInteger('pick_up_time_slot_id')->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->unsignedInteger('drop_off_time_slot_id')->nullable(false)->change();
            $table->unsignedInteger('pick_up_time_slot_id')->nullable(false)->change();
        });
    }
}