<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * AUDIT.md CC3: every money column is stored as INT or VARCHAR.
 *
 *   - INT columns silently round anything below 1 currency unit to 0.
 *     Hours of debugging time go into "the invoice is short by €0.45"
 *     issues that are really MySQL truncating €1247.45 → €1247 on insert.
 *   - VARCHAR columns coerce string-prefix-to-int on SUM() (a row of
 *     "12.50abc" is summed as 12), so totals quietly under-state revenue.
 *
 * Convert both to DECIMAL(15,2) — 15 significant digits, 2 fractional —
 * which can hold up to ~€9,999,999,999,999.99 with cent precision. This
 * is the canonical money type for MySQL.
 *
 * Pre-flight check (run against live DB before authoring this migration)
 * confirmed every VARCHAR money column already holds numeric-only strings
 * — zero rows would lose data on the cast. INT → DECIMAL(15,2) is always
 * lossless (e.g. 1000 → 1000.00).
 *
 * Reversal path: down() reverts to the pre-migration types. Existing
 * decimal-valued rows would round to int on the way back, so don't run
 * down() in production once a single non-integer-cent payment has been
 * recorded.
 */
return new class extends Migration {

    /**
     * table => [column => DECIMAL(precision, scale)]
     *
     * `default` is set to '0.00' on every column because the legacy
     * INT/VARCHAR columns mostly had no default and would receive NULL
     * on incomplete inserts. DECIMAL with a default keeps the new
     * columns safe for that pattern.
     */
    protected array $moneyColumns = [
        'supplier_invoices'       => ['total_amount'],
        'client_invoices'         => ['amount_receiveable'],
        'invoice_items'           => ['amount', 'total_amount'],
        'transactions'            => ['amount'],
        'office_balance'          => ['total_amount'],
        'office_utility_expenses' => ['monthly_expense'],
        'office_employes_salary'  => ['employe_salary'],
        'office_tours'            => ['tour_expenses'],
        'office_earnings'         => ['revenue', 'profit'],
        'office_invoices'         => ['officeinvoice_amount'],
    ];

    public function up(): void
    {
        foreach ($this->moneyColumns as $table => $cols) {
            if (!Schema::hasTable($table)) {
                continue;
            }
            foreach ($cols as $col) {
                if (!Schema::hasColumn($table, $col)) {
                    continue;
                }
                // Coerce VARCHAR cells to numeric before the ALTER so
                // MySQL's strict-mode doesn't reject the conversion on
                // empty strings.
                DB::statement("UPDATE `$table` SET `$col` = 0 WHERE `$col` IS NULL OR `$col` = ''");

                // We use raw SQL rather than $table->decimal() change()
                // because the project doesn't require doctrine/dbal at
                // its constrained version for every column type.
                DB::statement("ALTER TABLE `$table` MODIFY `$col` DECIMAL(15, 2) NOT NULL DEFAULT 0.00");
            }
        }
    }

    public function down(): void
    {
        // Reverse map back to the pre-migration types. Note: decimal
        // values >= 0.50 will round UP on the cast back to INT.
        $reverse = [
            'supplier_invoices'       => ['total_amount' => 'INT(100)'],
            'client_invoices'         => ['amount_receiveable' => 'INT(100)'],
            'invoice_items'           => ['amount' => 'INT(100)', 'total_amount' => 'INT(200)'],
            'transactions'            => ['amount' => 'INT(11)'],
            'office_balance'          => ['total_amount' => 'INT(100)'],
            'office_utility_expenses' => ['monthly_expense' => 'INT(100)'],
            'office_employes_salary'  => ['employe_salary' => 'INT(100)'],
            'office_tours'            => ['tour_expenses' => 'INT(100)'],
            'office_earnings'         => ['revenue' => 'VARCHAR(200)', 'profit' => 'VARCHAR(200)'],
            'office_invoices'         => ['officeinvoice_amount' => 'VARCHAR(55)'],
        ];

        foreach ($reverse as $table => $cols) {
            if (!Schema::hasTable($table)) continue;
            foreach ($cols as $col => $type) {
                if (!Schema::hasColumn($table, $col)) continue;
                DB::statement("ALTER TABLE `$table` MODIFY `$col` $type");
            }
        }
    }
};
