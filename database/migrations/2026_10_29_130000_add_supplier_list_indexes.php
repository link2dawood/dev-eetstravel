<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Supplier lists and the supplier search join countries on alias and sort by name, but neither
 * column was indexed: every page did a full scan of countries per supplier row plus a filesort
 * of all suppliers. Plain (non-unique) indexes only; no data changes.
 */
class AddSupplierListIndexes extends Migration
{
    const INDEXES = [
        ['countries', 'alias'],
        ['restaurants', 'name'],
        ['events', 'name'],
        ['hotels', 'name'],
        ['guides', 'name'],
        ['transfers', 'name'],
    ];

    public function up()
    {
        if (DB::getDriverName() !== 'mysql') return;
        foreach (self::INDEXES as [$table, $column]) {
            if (Schema::hasTable($table) && Schema::hasColumn($table, $column) && !$this->hasIndexOn($table, $column)) {
                Schema::table($table, function ($blueprint) use ($table, $column) {
                    $blueprint->index($column, "{$table}_{$column}_index");
                });
            }
        }
    }

    public function down()
    {
        if (DB::getDriverName() !== 'mysql') return;
        foreach (self::INDEXES as [$table, $column]) {
            $name = "{$table}_{$column}_index";
            if (Schema::hasTable($table) && collect(DB::select("SHOW INDEX FROM `{$table}`"))->contains('Key_name', $name)) {
                Schema::table($table, function ($blueprint) use ($name) {
                    $blueprint->dropIndex($name);
                });
            }
        }
    }

    /** Any existing index whose first column is $column already serves these queries */
    private function hasIndexOn($table, $column)
    {
        return collect(DB::select("SHOW INDEX FROM `{$table}`"))
            ->contains(function ($index) use ($column) {
                return $index->Column_name === $column && (int) $index->Seq_in_index === 1;
            });
    }
}
