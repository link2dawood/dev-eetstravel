<?php
// Record a local pre-conversion snapshot and verify the targeted DR11 migration.
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
use Illuminate\Support\Facades\DB;
if (DB::getDriverName() !== 'mysql') throw new RuntimeException('MySQL required');
$tables = ['client_invoices', 'invoices_tours', 'office_fees', 'supplier_invoices'];
$snapshot = function () use ($tables) {
    $data = [];
    foreach ($tables as $table) $data[$table] = DB::table($table)->orderBy('id')->get()->toArray();
    return $data;
};
$before = $snapshot();
$path = storage_path('app/driver-finance-before-' . date('Ymd-His') . '.json');
file_put_contents($path, json_encode($before, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR));
require __DIR__ . '/../database/migrations/2026_10_30_130000_convert_driver_audit_finance_tables_to_utf8mb4.php';
// Artisan records the migration; do not execute unrelated pending migrations.
$exit = Illuminate\Support\Facades\Artisan::call('migrate', ['--path' => 'database/migrations/2026_10_30_130000_convert_driver_audit_finance_tables_to_utf8mb4.php', '--force' => true]);
echo Illuminate\Support\Facades\Artisan::output();
// Idempotent readback repair also covers an already recorded local migration.
(new ConvertDriverAuditFinanceTablesToUtf8mb4)->up();
if ($exit || serialize($before) !== serialize($snapshot())) throw new RuntimeException('Migration failed or stored values changed; inspect the local snapshot.');
echo 'PASS: all four tables retain identical row values after conversion.' . PHP_EOL;
