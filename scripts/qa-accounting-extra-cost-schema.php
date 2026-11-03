<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;
if (DB::getDriverName() !== 'mysql') throw new RuntimeException('MySQL required');
$snapshot = function () {
    $rows = DB::table('client_invoices')->orderBy('id')->get()->toArray();
    foreach ($rows as $row) if ($row->extra_cost !== null) $row->extra_cost = number_format((float)$row->extra_cost, 2, '.', '');
    return serialize($rows);
};
$before = $snapshot();
$exit = Artisan::call('migrate', ['--path' => 'database/migrations/2026_10_14_000001_preserve_client_invoice_extra_cost_decimals.php', '--force' => true]);
echo Artisan::output();
if ($exit || $before !== $snapshot()) throw new RuntimeException('Unexpected invoice value change during schema widening');
echo 'PASS: existing invoice values preserved while enabling extra-cost cents.' . PHP_EOL;
