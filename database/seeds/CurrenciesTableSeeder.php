<?php

use Illuminate\Database\Seeder;

class CurrenciesTableSeeder extends Seeder
{
    public static $currencies = [
        [ 'name' => 'Australia Dollar', 'code' => 'AUD', 'symbol' => 'A$', 'cent' => 'c', 'symbol_cent' => '100'],
        [ 'name' => 'Bulgaria Leva', 'code' => 'BGN', 'symbol' => 'Ð»Ð²', 'cent' => '', 'symbol_cent' => ''],
        [ 'name' => 'Brazil Reai', 'code' => 'BRL', 'symbol' => 'R$', 'cent' => '', 'symbol_cent' => ''],
        [ 'name' => 'Canada Dollar', 'code' => 'CAD', 'symbol' => 'C$', 'cent' => 'Â¢', 'symbol_cent' => '100'],
        [ 'name' => 'Switzerland Franc', 'code' => 'CHF', 'symbol' => 'Sâ‚£', 'cent' => 'Rp', 'symbol_cent' => '100'],
        [ 'name' => 'China Yuan Renminbi', 'code' => 'CNY', 'symbol' => 'Â¥', 'cent' => 'åˆ†', 'symbol_cent' => '100'],
        [ 'name' => 'Czech Republic Koruny', 'code' => 'CZK', 'symbol' => 'KÄ', 'cent' => '', 'symbol_cent' => ''],
        [ 'name' => 'Denmark Kroner', 'code' => 'DKK', 'symbol' => 'kr', 'cent' => '', 'symbol_cent' => ''],
        [ 'name' => 'Estonia Krooni', 'code' => 'EEK', 'symbol' => 'kr', 'cent' => '', 'symbol_cent' => ''],
        [ 'name' => 'Euro', 'code' => 'EUR', 'symbol' => 'â‚¬', 'cent' => 'cent', 'symbol_cent' => '100'],
    ];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach (self::$currencies as $currency ) {
            DB::table('currencies')->insert([
                'name'       => $currency['name'],
                'code'       => $currency['code'],
                'symbol'  => $currency['symbol'],
                'cent'  => $currency['cent'],
                'symbol_cent'  => $currency['symbol_cent'],
                'created_at' => date('Y-m-d h:i:s'),
                'updated_at' => date('Y-m-d h:i:s')
            ]);
        }

    }
}
