<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCapacityAndCommentsToBuses extends Migration
{
    public function up()
    {
        Schema::table('buses', function (Blueprint $table) {
            $table->unsignedSmallInteger('capacity')->nullable();
            $table->text('comments')->nullable();
        });
    }

    public function down()
    {
        Schema::table('buses', function (Blueprint $table) {
            $table->dropColumn(['capacity', 'comments']);
        });
    }
}
