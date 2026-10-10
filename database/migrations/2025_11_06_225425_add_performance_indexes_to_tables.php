<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddPerformanceIndexesToTables extends Migration
{
    /**
     * index name => [table, columns]
     *
     * Each index is added only when its table and columns exist and the index is not
     * there yet: tour_packages has no tour_day_id/hotel/transfer columns, which made
     * this migration fail halfway and then fail again on the indexes it had created.
     */
    private $indexes = [
        'idx_tours_status_departure' => ['tours', ['status', 'departure_date']],
        'idx_tours_client' => ['tours', ['client_id']],
        'idx_tours_responsible' => ['tours', ['responsible']],
        'idx_tours_created' => ['tours', ['created_at']],
        'idx_tasks_status_deadline' => ['tasks', ['status', 'dead_line']],
        'idx_tasks_tour' => ['tasks', ['tour']],
        'idx_tasks_assign' => ['tasks', ['assign']],
        'idx_tasks_created' => ['tasks', ['created_at']],
        'idx_tour_packages_day' => ['tour_packages', ['tour_day_id']],
        'idx_tour_packages_hotel' => ['tour_packages', ['hotel']],
        'idx_tour_packages_transfer' => ['tour_packages', ['transfer']],
        'idx_users_email' => ['users', ['email']],
        'idx_notifications_created' => ['notifications', ['created_at']],
    ];

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        foreach ($this->indexes as $name => [$table, $columns]) {
            if (!Schema::hasTable($table) || !Schema::hasColumns($table, $columns) || $this->indexExists($table, $name)) {
                continue;
            }
            Schema::table($table, function ($blueprint) use ($columns, $name) {
                $blueprint->index($columns, $name);
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
        foreach ($this->indexes as $name => [$table]) {
            if (Schema::hasTable($table) && $this->indexExists($table, $name)) {
                Schema::table($table, function ($blueprint) use ($name) {
                    $blueprint->dropIndex($name);
                });
            }
        }
    }

    private function indexExists($table, $name)
    {
        return !empty(DB::select("SHOW INDEX FROM `{$table}` WHERE Key_name = ?", [$name]));
    }
}
