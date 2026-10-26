<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateEpicsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (! Schema::hasTable('epics')) {
            Schema::create('epics', function (Blueprint $table) {
                // int unsigned, so it matches tasks.epic_id and the foreign key below
                // can actually be created on MySQL.
                $table->increments('id');
                $table->timestamps();
            });
        }

        // tasks.epic_id is added by 2025_10_23_211354_add_monday_fields_to_tasks_table,
        // which runs before this file, so this is the first point where both sides of
        // the relation exist.
        if ($this->canLinkTasksToEpics()) {
            Schema::table('tasks', function (Blueprint $table) {
                $table->foreign('epic_id')->references('id')->on('epics')->onDelete('set null');
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
        Schema::dropIfExists('epics');
    }

    /**
     * Only add the foreign key when both columns exist, no key is there yet and the
     * column types match, so an older database that already has an epics table with a
     * different id type is left alone instead of failing the migration.
     */
    private function canLinkTasksToEpics(): bool
    {
        if (! Schema::hasTable('tasks') || ! Schema::hasColumn('tasks', 'epic_id')) {
            return false;
        }

        if (DB::getDriverName() !== 'mysql') {
            // Other drivers (SQLite in tests) ignore ALTER TABLE ... ADD FOREIGN KEY.
            return true;
        }

        $alreadyLinked = DB::table('information_schema.KEY_COLUMN_USAGE')
            ->where('TABLE_SCHEMA', DB::getDatabaseName())
            ->where('TABLE_NAME', 'tasks')
            ->where('COLUMN_NAME', 'epic_id')
            ->whereNotNull('REFERENCED_TABLE_NAME')
            ->exists();

        if ($alreadyLinked) {
            return false;
        }

        $taskColumn = $this->columnType('tasks', 'epic_id');

        return $taskColumn !== null && $taskColumn === $this->columnType('epics', 'id');
    }

    private function columnType(string $table, string $column): ?string
    {
        $row = DB::table('information_schema.COLUMNS')
            ->where('TABLE_SCHEMA', DB::getDatabaseName())
            ->where('TABLE_NAME', $table)
            ->where('COLUMN_NAME', $column)
            ->first(['COLUMN_TYPE']);

        return $row === null ? null : strtolower($row->COLUMN_TYPE);
    }
}
