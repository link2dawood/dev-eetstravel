<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;
if (DB::getDriverName() !== 'mysql') throw new RuntimeException('MySQL required');
$before = DB::table('rates')->orderBy('id')->get()->toArray();
$validIds = DB::table('rate_types')->pluck('id')->map(fn($id) => (string)$id)->all();
file_put_contents(storage_path('app/rates-before-type-normalization-' . date('Ymd-His') . '.json'), json_encode($before, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR));
$expected = unserialize(serialize($before)); $changed = 0;
foreach ($expected as $rate) {
    if ($rate->rate_type !== null && !in_array((string)$rate->rate_type, $validIds, true)) { $rate->rate_type = null; $changed++; }
}
$exit = Artisan::call('migrate', ['--path' => 'database/migrations/2026_10_30_140000_normalize_invalid_rate_type_references.php', '--force' => true]);
echo Artisan::output();
if ($exit || serialize($expected) !== serialize(DB::table('rates')->orderBy('id')->get()->toArray())) throw new RuntimeException('Unexpected data change; inspect the local pre-migration snapshot.');
echo "PASS: $changed invalid rate-type values normalized; all other rate values preserved." . PHP_EOL;
