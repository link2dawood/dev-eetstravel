<?php
// Apply only the supplier extra-cost precision migration and compare all stored values.
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
use Illuminate\Support\Facades\DB;
if (DB::getDriverName() !== 'mysql') throw new RuntimeException('MySQL required');
$before = DB::table('supplier_invoices')->orderBy('id')->get(['id', 'total_amount', 'extra_amount'])->mapWithKeys(function ($row) {
    return [$row->id => ['total_amount' => (string) $row->total_amount, 'extra_amount' => $row->extra_amount === null ? null : (string) $row->extra_amount]];
})->all();
Artisan::call('migrate', ['--path' => 'database/migrations/2026_10_30_200000_preserve_supplier_invoice_extra_cost_decimals.php', '--force' => true]);
Artisan::call('migrate', ['--path' => 'database/migrations/2026_10_30_210000_preserve_supplier_invoice_total_decimals.php', '--force' => true]);
$after = DB::table('supplier_invoices')->orderBy('id')->get(['id', 'total_amount', 'extra_amount'])->mapWithKeys(function ($row) {
    return [$row->id => ['total_amount' => number_format((float) $row->total_amount, 2, '.', ''), 'extra_amount' => $row->extra_amount === null ? null : number_format((float) $row->extra_amount, 2, '.', '')]];
})->all();
$expected = array_map(function ($values) { return ['total_amount' => number_format((float) $values['total_amount'], 2, '.', ''), 'extra_amount' => $values['extra_amount'] === null ? null : number_format((float) $values['extra_amount'], 2, '.', '')]; }, $before);
if ($expected !== $after) throw new RuntimeException('Existing supplier invoice extra costs changed');
echo 'Passed: existing supplier invoice totals and extra costs were preserved; both columns now store cents.' . PHP_EOL;
