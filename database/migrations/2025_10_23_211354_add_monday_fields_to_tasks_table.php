<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMondayFieldsToTasksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tasks', function (Blueprint $table) {
            // Add epic_id column if it doesn't exist. The foreign key is added by
            // 2025_10_23_211536_create_epics_table, which is where the epics table
            // first exists: adding it here fails with "Failed to open the referenced
            // table 'epics'" on any database that has not run that migration yet.
            if (!Schema::hasColumn('tasks', 'epic_id')) {
                $table->unsignedInteger('epic_id')->nullable()->after('priority');
            }

            // Add story_points column if it doesn't exist
            if (!Schema::hasColumn('tasks', 'story_points')) {
                $table->integer('story_points')->default(0)->nullable()->after('epic_id');
            }

            // Add estimated_sp column if it doesn't exist (same as story_points, for compatibility)
            if (!Schema::hasColumn('tasks', 'estimated_sp')) {
                $table->integer('estimated_sp')->default(0)->nullable()->after('story_points');
            }

            // Add sort_order for manual sorting within groups
            if (!Schema::hasColumn('tasks', 'sort_order')) {
                $table->integer('sort_order')->default(0)->after('estimated_sp');
            }

            // Add description field for detailed task info
            if (!Schema::hasColumn('tasks', 'description')) {
                $table->text('description')->nullable()->after('content');
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
        Schema::table('tasks', function (Blueprint $table) {
            if (Schema::hasColumn('tasks', 'epic_id')) {
                $table->dropForeign(['epic_id']);
                $table->dropColumn('epic_id');
            }
            if (Schema::hasColumn('tasks', 'story_points')) {
                $table->dropColumn('story_points');
            }
            if (Schema::hasColumn('tasks', 'estimated_sp')) {
                $table->dropColumn('estimated_sp');
            }
            if (Schema::hasColumn('tasks', 'sort_order')) {
                $table->dropColumn('sort_order');
            }
            if (Schema::hasColumn('tasks', 'description')) {
                $table->dropColumn('description');
            }
        });
    }
}
