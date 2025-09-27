<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCruisesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cruises', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('date_from')->nullable();
            $table->string('date_to')->nullable();
            $table->string('city_to')->nullable();
            $table->string('city_from')->nullable();
            $table->string('country_from')->nullable();
            $table->string('country_to')->nullable();
            $table->string('address_first')->nullable();
            $table->string('address_second')->nullable();
            $table->string('code')->nullable();
            $table->string('work_phone')->nullable();
            $table->string('work_fax')->nullable();
            $table->string('work_email')->nullable();
            $table->string('contact_name')->nullable();
            $table->string('contact_phone')->nullable();
            $table->string('contact_email')->nullable();
            $table->longText('comments')->nullable();
            $table->string('int_comments')->nullable();
            $table->string('data_cb')->nullable();
            $table->string('data_cd')->nullable();
            $table->string('data_code')->nullable();
            $table->string('data_ct')->nullable();
            $table->string('data_lmb')->nullable();
            $table->string('data_lmd')->nullable();
            $table->string('data_lmt')->nullable();
            $table->string('website')->nullable();
            $table->string('company')->nullable();
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
        Schema::dropIfExists('cruises');
    }
}
