<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTourPackagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tour_packages', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->nullable();
            $table->longText('description')->nullable();
            $table->integer('status')->nullable();
            $table->boolean('paid')->nullable();
            $table->integer('pax')->nullable();
            $table->integer('pax_free')->nullable();
            $table->integer('total_amount')->default(0);
            $table->integer('currency')->nullable();
            $table->datetime('time_from')->nullable();
            $table->datetime('time_to')->nullable();
            $table->string('rate')->nullable();
            $table->text('note')->nullable();
            $table->integer('type')->nullable();
            $table->integer('reference')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tour_packages');
    }
}
