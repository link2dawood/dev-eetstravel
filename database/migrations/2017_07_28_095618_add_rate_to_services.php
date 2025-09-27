<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddRateToServices extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('hotels', function (Blueprint $table) {
            $table->string('rate')->nullable();
        });

        Schema::table('restaurants', function (Blueprint $table) {
            $table->string('rate')->nullable();
        });

        Schema::table('events', function (Blueprint $table) {
            $table->string('rate')->nullable();
        });

        Schema::table('guides', function (Blueprint $table) {
            $table->string('rate')->nullable();
        });

        Schema::table('transfers', function (Blueprint $table) {
            $table->string('rate')->nullable();
        });

        Schema::table('cruises', function (Blueprint $table) {
            $table->string('rate')->nullable();
        });

        Schema::table('flights', function (Blueprint $table) {
            $table->string('rate')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('hotels', function (Blueprint $table) {
            $table->dropColumn('rate');
        });

        Schema::table('restaurants', function (Blueprint $table) {
            $table->dropColumn('rate');
        });

        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn('rate');
        });

        Schema::table('guides', function (Blueprint $table) {
            $table->dropColumn('rate');
        });

        Schema::table('transfers', function (Blueprint $table) {
            $table->dropColumn('rate');
        });

        Schema::table('cruises', function (Blueprint $table) {
            $table->dropColumn('rate');
        });

        Schema::table('flights', function (Blueprint $table) {
            $table->dropColumn('rate');
        });
    }
}
