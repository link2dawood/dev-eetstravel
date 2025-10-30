<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStatusFlagsToStatusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('status', function (Blueprint $table) {
            if (!Schema::hasColumn('status', 'is_completed')) {
                $table->boolean('is_completed')->default(false)->after('color');
            }
            if (!Schema::hasColumn('status', 'is_aborted')) {
                $table->boolean('is_aborted')->default(false)->after('is_completed');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('status', function (Blueprint $table) {
            if (Schema::hasColumn('status', 'is_completed')) {
                $table->dropColumn('is_completed');
            }
            if (Schema::hasColumn('status', 'is_aborted')) {
                $table->dropColumn('is_aborted');
            }
        });
    }
}
