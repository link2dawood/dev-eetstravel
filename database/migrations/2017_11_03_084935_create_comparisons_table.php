<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateComparisonsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('comparisons', function (Blueprint $table) {
            $table->increments('id');
            $table->string('hotel_list_sent')->nullable();
            $table->string('front_sheet_sent')->nullable();
            $table->string('visa_confirmation_sent')->nullable();
            $table->string('group_fix')->nullable();
            $table->string('rooming_list_received')->nullable();
            $table->string('final_documents_sent')->nullable();
            $table->text('comments')->nullable();
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
        Schema::dropIfExists('comparisons');
    }
}
