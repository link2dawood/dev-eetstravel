<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHollydaycalendarsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hollydaycalendars', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('googlecalendarid');
            $table->string('color');
            $table->boolean('allday')->default(true);
            $table->boolean('checked')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('hollydaycalendars');
    }
}
