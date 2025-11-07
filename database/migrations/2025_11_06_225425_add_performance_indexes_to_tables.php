<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPerformanceIndexesToTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Add indexes to tours table for better performance
        Schema::table('tours', function (Blueprint $table) {
            $table->index(['status', 'departure_date'], 'idx_tours_status_departure');
            $table->index('client_id', 'idx_tours_client');
            $table->index('responsible', 'idx_tours_responsible');
            $table->index('created_at', 'idx_tours_created');
        });

        // Add indexes to tasks table for better performance
        Schema::table('tasks', function (Blueprint $table) {
            $table->index(['status', 'dead_line'], 'idx_tasks_status_deadline');
            $table->index('tour', 'idx_tasks_tour');
            $table->index('assign', 'idx_tasks_assign');
            $table->index('created_at', 'idx_tasks_created');
        });

        // Add indexes to tour_packages table
        Schema::table('tour_packages', function (Blueprint $table) {
            $table->index('tour_day_id', 'idx_tour_packages_day');
            $table->index('hotel', 'idx_tour_packages_hotel');
            $table->index('transfer', 'idx_tour_packages_transfer');
        });

        // Add indexes to users table
        Schema::table('users', function (Blueprint $table) {
            $table->index('email', 'idx_users_email');
        });

        // Add indexes to notifications table if it exists
        if (Schema::hasTable('notifications')) {
            Schema::table('notifications', function (Blueprint $table) {
                $table->index('created_at', 'idx_notifications_created');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Remove indexes from tours table
        Schema::table('tours', function (Blueprint $table) {
            $table->dropIndex('idx_tours_status_departure');
            $table->dropIndex('idx_tours_client');
            $table->dropIndex('idx_tours_responsible');
            $table->dropIndex('idx_tours_created');
        });

        // Remove indexes from tasks table
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropIndex('idx_tasks_status_deadline');
            $table->dropIndex('idx_tasks_tour');
            $table->dropIndex('idx_tasks_assign');
            $table->dropIndex('idx_tasks_created');
        });

        // Remove indexes from tour_packages table
        Schema::table('tour_packages', function (Blueprint $table) {
            $table->dropIndex('idx_tour_packages_day');
            $table->dropIndex('idx_tour_packages_hotel');
            $table->dropIndex('idx_tour_packages_transfer');
        });

        // Remove indexes from users table
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex('idx_users_email');
        });

        // Remove indexes from notifications table if it exists
        if (Schema::hasTable('notifications')) {
            Schema::table('notifications', function (Blueprint $table) {
                $table->dropIndex('idx_notifications_created');
            });
        }
    }
}
