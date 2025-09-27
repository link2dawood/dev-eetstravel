<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateComparisonRowsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('comparison_rows', function (Blueprint $table) {
            $table->increments('id');
	        $table->integer('comparison_id')->nullable()->index()->unsigned();
	        $table->foreign('comparison_id')->references('id')->on('comparisons')->onDelete('cascade');
	        $table->boolean('rooming_list_reserved');
	        $table->boolean('visa_confirmation');
	        $table->date('date');
            $table->timestamps();
        });

	    Schema::table('comparisons', function (Blueprint $table) {
		    $table->date('hotel_list_sent')->change();
		    $table->date('front_sheet_sent')->change();
		    $table->date('visa_confirmation_sent')->change();
		    $table->date('group_fix')->change();
		    $table->date('rooming_list_received')->change();
		    $table->date('final_documents_sent')->change();
	    });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('comparison_rows');
    }
}
