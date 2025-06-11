<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateListOfTimeSlotsTable extends Migration
{
    public function up()
    {
        Schema::create('list_of_time_slots', function (Blueprint $table) {
            $table->increments('id');
            $table->text('time_slot');
        });
    }

    public function down()
    {
        Schema::dropIfExists('list_of_time_slots');
    }
}