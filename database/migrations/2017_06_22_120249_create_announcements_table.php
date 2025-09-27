<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAnnouncementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('announcements', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title');
            $table->text('content');
            $table->integer('author')->unassigned();
            $table->integer('parent_id')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::table('files', function (Blueprint $table) {
            $table->integer('announcement_id')->nullable();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('files', function (Blueprint $table) {
            $table->dropColumn('announcement_id');

        });
        Schema::dropIfExists('announcements');
    }
}
