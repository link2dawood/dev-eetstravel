<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class ConvertDriverAuditFinanceTablesToUtf8mb4 extends Migration
{
    public function up()
    {
        if (DB::getDriverName() !== 'mysql') return;
        foreach (['client_invoices', 'invoices_tours', 'office_fees', 'supplier_invoices'] as $table) {
            $metadata = DB::selectOne('SELECT TABLE_COLLATION AS collation FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ?', [$table]);
            if (!$metadata) continue;
            $convert = strpos($metadata->collation, 'utf8mb4') !== 0;
            $columns = DB::select('SELECT CHARACTER_SET_NAME AS charset FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND CHARACTER_SET_NAME IS NOT NULL', [$table]);
            foreach ($columns as $column) {
                if ($column->charset !== 'utf8mb4') $convert = true;
            }
            if ($convert) DB::statement('ALTER TABLE `' . $table . '` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');
        }
    }

    public function down()
    {
        // Retain Unicode support: narrowing the character set would discard text.
    }
}
