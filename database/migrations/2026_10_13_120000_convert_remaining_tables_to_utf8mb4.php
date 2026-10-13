<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Some tables were created as latin1 or 3-byte utf8, so characters such as
 * "Kč", "ő" or "Łódź" were saved as "?" (office addresses, invoice notes,
 * supplier comments...). Converts every table of this database that is not
 * utf8mb4 yet; CONVERT TO keeps the existing text (latin1 "Südtirolerplatz"
 * stays "Südtirolerplatz"). Safe to run again: converted tables are skipped.
 */
class ConvertRemainingTablesToUtf8mb4 extends Migration
{
    public function up()
    {
        if (DB::getDriverName() !== 'mysql') {
            return;
        }

        $tables = DB::select(
            "SELECT TABLE_NAME AS t FROM information_schema.TABLES
             WHERE TABLE_SCHEMA = DATABASE() AND TABLE_TYPE = 'BASE TABLE'
               AND TABLE_COLLATION IS NOT NULL AND TABLE_COLLATION NOT LIKE 'utf8mb4%'"
        );

        foreach ($tables as $table) {
            DB::statement('ALTER TABLE `' . str_replace('`', '``', $table->t) . '` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');
        }
    }

    public function down()
    {
        // Not reversible without losing characters that latin1/utf8 cannot hold.
    }
}
