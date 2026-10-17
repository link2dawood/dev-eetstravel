<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Sample data for the Base Input menu (statuses, room types, rates, currencies,
 * currency rates and criteria). Records that have a name are called
 * "QA Sample ..."; the ids of everything created are kept in
 * storage/app/sample-data/base-input.json so `--remove` deletes exactly those.
 *
 * The currency rates are example values (EUR base, dated today), not live rates.
 */
class BaseInputSampleData extends Command
{
    protected $signature = 'base:sample-data {--remove : Delete the sample records instead of creating them} {--safe : Keep existing currency codes and exchange rates unchanged}';

    protected $description = 'Create (or remove) QA Sample statuses, room types, rates, currencies, currency rates and criteria';

    const PREFIX = 'QA Sample ';

    // [name, type, color, sort order]
    const STATUSES = [
        ['Awaiting Deposit', 'tour', null, 50],
        ['Waiting for Supplier', 'task', null, 50],
        ['Rooms Released', 'hotel', '#f59f00', 50],
        ['Driver Assigned', 'bus', '#6f42c1', 6],
        ['Offer Sent to Client', 'offer', null, 50],
    ];

    // [name, code, icon file in public/assets/img/svg, sort order]
    const ROOM_TYPES = [
        ['Quadruple', 'QUA', 'triple.svg', 20],
        ['Family Room', 'FAM', 'twin-rooms.svg', 21],
        ['Studio', 'STU', 'single-room.svg', 22],
    ];

    // [name, mark, rate type id, sort order]
    const RATES = [
        ['4* Superior', '4*S', 1, 50],
        ['Boutique', 'BTQ', 1, 51],
        ['Group Rate', 'GRP', 4, 52],
    ];

    // [name, code, symbol, cent, symbol_cent]
    const CURRENCIES = [
        ['US Dollar', 'USD', '$', '¢', '100'],
        ['Polish Zloty', 'PLN', 'zł', 'gr', '100'],
        ['Hungarian Forint', 'HUF', 'Ft', 'fillér', '100'],
        ['British Pound', 'GBP', '£', 'p', '100'],
    ];

    // currency => example rate against EUR
    const CURRENCY_RATES = [
        'CZK' => 24.31, 'HUF' => 395.4, 'PLN' => 4.27, 'GBP' => 0.864, 'CHF' => 0.938, 'USD' => 1.092, 'BGN' => 1.9558, 'DKK' => 7.4605,
    ];

    // [name, short name, icon, criteria type id]
    const CRITERIA = [
        ['Free Wi-Fi', 'Wi-Fi', 'wifi', 1],
        ['Wheelchair Access', 'Accessible', 'wheelchair', 1],
        ['Audio Guide', 'Audio', 'headphones', 2],
        ['Vegetarian Menu', 'Veggie', 'leaf', 3],
        ['Coach Parking', 'Coach P', 'bus', 3],
    ];

    public function handle()
    {
        return $this->option('remove') ? $this->remove() : $this->create();
    }

    private function manifestPath()
    {
        return storage_path('app/sample-data/base-input.json');
    }

    private function create()
    {
        if (is_file($this->manifestPath())) {
            $this->error('Base Input sample data already exists. Run with --remove first.');
            return 1;
        }

        $now = Carbon::now();
        $today = $now->toDateString();
        $made = DB::transaction(function () use ($now, $today) {
            $ids = [];
            foreach (self::STATUSES as [$name, $type, $color, $sort]) {
                $ids['status'][] = DB::table('status')->insertGetId([
                    'name' => self::PREFIX . $name, 'type' => $type, 'color' => $color, 'sort_order' => $sort,
                    'status' => 0, 'is_completed' => 0, 'is_aborted' => 0, 'created_at' => $now, 'updated_at' => $now,
                ]);
            }
            foreach (self::ROOM_TYPES as [$name, $code, $img, $sort]) {
                $ids['room_types'][] = DB::table('room_types')->insertGetId([
                    'name' => self::PREFIX . $name, 'code' => $code, 'img' => $img, 'sort_order' => $sort, 'created_at' => $now, 'updated_at' => $now,
                ]);
            }
            foreach (self::RATES as [$name, $mark, $type, $sort]) {
                $ids['rates'][] = DB::table('rates')->insertGetId([
                    'name' => self::PREFIX . $name, 'mark' => $mark, 'rate_type' => $type, 'sort_order' => $sort, 'created_at' => $now, 'updated_at' => $now,
                ]);
            }
            foreach (self::CURRENCIES as [$name, $code, $symbol, $cent, $symbolCent]) {
                if ($this->option('safe') && DB::table('currencies')->where('code', $code)->exists()) {
                    continue;
                }
                $ids['currencies'][] = DB::table('currencies')->insertGetId([
                    'name' => self::PREFIX . $name, 'code' => $code, 'symbol' => $symbol, 'cent' => $cent, 'symbol_cent' => $symbolCent,
                    'created_at' => $now, 'updated_at' => $now,
                ]);
            }
            foreach (self::CURRENCY_RATES as $currency => $rate) {
                if ($this->option('safe')) {
                    continue;
                }
                $ids['currency_rates'][] = DB::table('currency_rates')->insertGetId([
                    'currency' => $currency, 'rate' => $rate, 'date' => $today, 'created_at' => $now, 'updated_at' => $now,
                ]);
            }
            foreach (self::CRITERIA as [$name, $short, $icon, $type]) {
                $ids['criterias'][] = DB::table('criterias')->insertGetId([
                    'name' => self::PREFIX . $name, 'short_name' => $short, 'icon' => $icon, 'criteria_type' => $type,
                    'created_at' => $now, 'updated_at' => $now,
                ]);
            }

            return $ids;
        });

        if (!is_dir(dirname($this->manifestPath()))) {
            mkdir(dirname($this->manifestPath()), 0775, true);
        }
        file_put_contents($this->manifestPath(), json_encode($made, JSON_PRETTY_PRINT));

        $this->table(array_keys($made), [array_map('count', array_values($made))]);
        $this->line($this->option('safe')
            ? 'Existing currency codes and exchange rates were preserved.'
            : 'Currency rates are example values dated ' . $today . ' (EUR = 1), not live rates.');
        $this->info('Remove them with: php artisan base:sample-data --remove');

        return 0;
    }

    private function remove()
    {
        if (!is_file($this->manifestPath())) {
            $this->error('No Base Input sample data found (' . $this->manifestPath() . ').');
            return 1;
        }

        $ids = json_decode(file_get_contents($this->manifestPath()), true) ?: [];
        $counts = DB::transaction(function () use ($ids) {
            $counts = [];
            foreach ($ids as $table => $tableIds) {
                $counts[$table] = DB::table($table)->whereIn('id', $tableIds)->delete();
            }
            return $counts;
        });
        unlink($this->manifestPath());

        $this->table(array_keys($counts), [array_values($counts)]);

        return 0;
    }
}
