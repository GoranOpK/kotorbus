<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSystemConfigTable extends Migration
{
    public function up()
    {
        Schema::create('system_config', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 255)->unique();
            $table->integer('value');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
        });
    }

    public function down()
    {
        Schema::dropIfExists('system_config');
    }
}