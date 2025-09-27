<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeColumnTypeAddForeignKeyTransferTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('transfers', function (Blueprint $table){
            $table->unsignedInteger('city')->nullable()->change();
            //add missing foreign Key
            $table->foreign('city')->references('id')->on('cities');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('transfers', function (Blueprint $table){
            $table->dropForeign('transfers_city_foreign');
        });
        Schema::table('transfers', function (Blueprint $table){
            $table->string('city')->change();
        });
    }
}
