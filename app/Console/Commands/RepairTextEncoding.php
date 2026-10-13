<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

/**
 * Repairs text that an old import stored as double-encoded UTF-8, e.g.
 * "CharbonniÃ¨re" instead of "Charbonnière" or "Ã¢â€šÂ¬" instead of "€".
 *
 * A value is only changed when reading it back as Windows-1252 bytes gives
 * valid UTF-8 (repeated for values encoded several times over), so normal text
 * such as "SÃO PAULO" is never touched. Email folders, raw emails and file
 * names are left alone: they must match the mail server and the disk.
 *
 * Dry run unless --apply is given. Every change is written to a CSV under
 * storage/app/encoding-repair, which --restore=<file> puts back.
 */
class RepairTextEncoding extends Command
{
    protected $signature = 'data:repair-encoding
        {--table=* : Only these tables}
        {--apply : Write the changes (default is a dry run)}
        {--restore= : Undo an applied run from its CSV file}';

    protected $description = 'Fix garbled characters (double-encoded UTF-8) left by an old import';

    const TABLES = ['events', 'hotels', 'restaurants', 'guides', 'transfers', 'flights', 'cruises', 'buses', 'drivers',
        'countries', 'cities', 'currencies', 'tours', 'tour_packages', 'clients', 'comments', 'chat_messages', 'notifications', 'menus'];

    // Columns that hold names of files, credentials or addresses that must stay byte-for-byte
    const SKIP_COLUMN = '/(file|path|password|token|hash|email|url|website)/i';

    // A lead byte (U+00C2-U+00F4 read as Windows-1252) followed by a continuation byte
    const PATTERN = '/[\x{00C2}-\x{00F4}][\x{0080}-\x{00BF}\x{0152}\x{0153}\x{0160}\x{0161}\x{0178}\x{017D}\x{017E}\x{0192}\x{02C6}\x{02DC}\x{2013}-\x{203A}\x{20AC}\x{2122}]/u';

    public function handle()
    {
        if ($this->option('restore')) {
            return $this->restore($this->option('restore'));
        }

        $apply = (bool) $this->option('apply');
        $this->info($apply ? 'APPLY mode: changes will be written.' : 'DRY RUN: no changes will be written (use --apply).');

        $tables = $this->option('table') ?: self::TABLES;
        if ($unknown = array_diff($tables, self::TABLES)) {
            $this->error('Not repaired by this command: ' . implode(', ', $unknown));
            return 1;
        }

        $dir = storage_path('app/encoding-repair');
        if (!is_dir($dir)) {
            mkdir($dir, 0775, true);
        }
        $file = $dir . '/' . date('Ymd_His') . ($apply ? '_applied' : '_dry_run') . '.csv';
        $csv = fopen($file, 'w');
        fputcsv($csv, ['table', 'id', 'column', 'old_value', 'new_value']);

        $rows = [];
        foreach ($tables as $table) {
            if (!Schema::hasTable($table) || !Schema::hasColumn($table, 'id')) {
                continue;
            }
            $columns = collect(DB::select(
                "SELECT COLUMN_NAME c FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND DATA_TYPE IN ('varchar','char','text','mediumtext','longtext','tinytext')",
                [$table]
            ))->pluck('c')->reject(function ($column) {
                return preg_match(self::SKIP_COLUMN, $column);
            })->values();

            $fixed = 0;
            foreach ($columns as $column) {
                $query = DB::table($table)->select('id', $column)->where(function ($q) use ($column) {
                    foreach (['Ã', 'Â', 'â€', 'Å', 'Ä', 'Ð', 'Ñ', 'å', 'æ', 'ä'] as $marker) {
                        $q->orWhere($column, 'like', '%' . $marker . '%');
                    }
                });
                foreach ($query->cursor() as $row) {
                    $old = (string) $row->{$column};
                    $new = self::repair($old);
                    if ($new === $old) {
                        continue;
                    }
                    fputcsv($csv, [$table, $row->id, $column, $old, $new]);
                    if ($apply) {
                        DB::table($table)->where('id', $row->id)->update([$column => $new]);
                    }
                    $fixed++;
                }
            }
            if ($fixed) {
                $rows[] = [$table, $fixed];
            }
        }

        fclose($csv);
        $this->table(['table', 'values fixed'], $rows ?: [['-', 0]]);
        $this->line('Details: ' . $file);

        return 0;
    }

    public static function repair($value)
    {
        $fixed = $value;
        for ($round = 0; $round < 4 && preg_match(self::PATTERN, $fixed); $round++) {
            $bytes = @mb_convert_encoding($fixed, 'Windows-1252', 'UTF-8');
            if ($bytes === false || $bytes === $fixed || !mb_check_encoding($bytes, 'UTF-8')
                || mb_strlen($bytes, 'UTF-8') >= mb_strlen($fixed, 'UTF-8') || strpos($bytes, "\u{FFFD}") !== false) {
                break;
            }
            $fixed = $bytes;
        }

        return $fixed;
    }

    private function restore($file)
    {
        if (!is_file($file)) {
            $file = storage_path('app/encoding-repair/' . $file);
        }
        if (!is_file($file) || !Str::contains($file, '_applied')) {
            $this->error('Give the CSV of an applied run (storage/app/encoding-repair/*_applied.csv).');
            return 1;
        }

        $handle = fopen($file, 'r');
        $header = fgetcsv($handle);
        $restored = 0;
        while (($line = fgetcsv($handle)) !== false) {
            $row = array_combine($header, $line);
            if (!in_array($row['table'], self::TABLES, true) || preg_match(self::SKIP_COLUMN, $row['column'])) {
                continue;
            }
            DB::table($row['table'])->where('id', $row['id'])->update([$row['column'] => $row['old_value']]);
            $restored++;
        }
        fclose($handle);
        $this->info("Restored {$restored} values from {$file}");

        return 0;
    }
}
