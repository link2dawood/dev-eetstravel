<?php
require __DIR__.'/../vendor/autoload.php';
require __DIR__.'/lib/LegacyEncodingRepair.php';
$app = require __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
foreach (['currencies' => ['name','symbol','cent','symbol_cent'], 'events' => ['name'], 'guides' => ['name'], 'transfers' => ['name']] as $table => $fields) {
    foreach (DB::table($table)->select(array_merge(['id'], $fields))->get() as $row) {
        foreach ($fields as $field) {
            $value = $row->$field;
            if (is_string($value) && LegacyEncodingRepair::repair($value) !== $value) {
                echo json_encode(compact('table','field') + ['id'=>$row->id,'value'=>$value], JSON_UNESCAPED_UNICODE).PHP_EOL;
            }
        }
    }
}
