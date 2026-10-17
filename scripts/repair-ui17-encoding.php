<?php
require __DIR__.'/../vendor/autoload.php';
require __DIR__.'/lib/LegacyEncodingRepair.php';
$app = require __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$path = storage_path('app/ui-audit/ui17-encoding-backup.json');
if (in_array('--verify', $argv, true)) {
    $plan = json_decode(file_get_contents($path), true, 512, JSON_THROW_ON_ERROR);
    $examples = [];
    foreach ($plan as $item) {
        $current = (array)DB::table($item['table'])->where('id', $item['id'])->first();
        if ($current !== array_replace($item['before'], $item['changes'])) throw new RuntimeException('Record no longer matches verified repair.');
        if (empty($current['deleted_at']) && !isset($examples[$item['table']])) $examples[$item['table']] = ['id'=>$item['id'], 'changes'=>$item['changes']];
    }
    file_put_contents(storage_path('app/ui-audit/ui17-active-examples.json'), json_encode($examples, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    echo json_encode(['verified_rows'=>count($plan), 'active_examples'=>$examples], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT).PHP_EOL;
    exit;
}
if (!in_array('--apply', $argv, true)) {
    if (file_exists($path)) throw new RuntimeException('Backup already exists; refusing to overwrite.');
    $plan = [];
    foreach (['currencies' => ['name','symbol','cent','symbol_cent'], 'events' => ['name'], 'guides' => ['name'], 'transfers' => ['name']] as $table => $fields) {
        foreach (DB::table($table)->get() as $row) {
            $changes = [];
            foreach ($fields as $field) {
                if (!is_string($row->$field)) continue;
                $fixed = LegacyEncodingRepair::repair($row->$field);
                if ($fixed !== $row->$field) $changes[$field] = $fixed;
            }
            if ($changes) $plan[] = ['table'=>$table, 'id'=>$row->id, 'before'=>(array)$row, 'changes'=>$changes];
        }
    }
    file_put_contents($path, json_encode($plan, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR));
    $counts = [];
    foreach ($plan as $item) $counts[$item['table']] = ($counts[$item['table']] ?? 0) + count($item['changes']);
    echo json_encode(['planned_fields'=>$counts, 'backup'=>$path], JSON_PRETTY_PRINT).PHP_EOL;
    foreach (array_slice($plan, 0, 8) as $item) {
        foreach ($item['changes'] as $field=>$after) echo json_encode(['table'=>$item['table'],'id'=>$item['id'],'field'=>$field,'before'=>$item['before'][$field],'after'=>$after], JSON_UNESCAPED_UNICODE).PHP_EOL;
    }
    exit;
}
$plan = json_decode(file_get_contents($path), true, 512, JSON_THROW_ON_ERROR);
DB::transaction(function () use ($plan) {
    foreach ($plan as $item) {
        $query = DB::table($item['table'])->where('id', $item['id']);
        $current = (array)$query->lockForUpdate()->first();
        if ($current !== $item['before']) throw new RuntimeException('Record changed since backup; aborting repair.');
        if ($query->update($item['changes']) !== 1) throw new RuntimeException('Unexpected update count.');
        $expected = array_replace($item['before'], $item['changes']);
        if ((array)$query->first() !== $expected) throw new RuntimeException('Unexpected record difference; rolling back.');
    }
});
file_put_contents(storage_path('app/ui-audit/ui17-encoding-applied.json'), json_encode(['rows'=>count($plan),'applied_at'=>date(DATE_ATOM)], JSON_PRETTY_PRINT));
echo 'Encoding repaired in '.count($plan).' rows. Every non-target field verified unchanged.'.PHP_EOL;
