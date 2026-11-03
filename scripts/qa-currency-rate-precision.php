<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;
if (DB::getDriverName() !== 'mysql') throw new RuntimeException('MySQL required');
$before = DB::table('currency_rates')->orderBy('id')->get()->toArray();
file_put_contents(storage_path('app/currency-rates-before-precision-' . date('Ymd-His') . '.json'), json_encode($before, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR));
$exit = Artisan::call('migrate', ['--path' => 'database/migrations/2026_10_13_130000_widen_currency_rates_rate_precision.php', '--force' => true]);
echo Artisan::output();
$after = DB::table('currency_rates')->orderBy('id')->get()->toArray();
foreach ([$before, $after] as $index => $rows) foreach ($rows as $row) $row->rate = number_format((float)$row->rate, 6, '.', '');
if ($exit || serialize($before) !== serialize($after)) throw new RuntimeException('Unexpected row change; inspect the pre-migration snapshot.');
$column = DB::selectOne("SELECT COLUMN_TYPE AS type FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'currency_rates' AND COLUMN_NAME = 'rate'");
if (!$column || $column->type !== 'decimal(18,6)') throw new RuntimeException('Expected decimal(18,6)');
echo 'PASS: rate is decimal(18,6); all existing row values preserved to six decimal places.' . PHP_EOL;
