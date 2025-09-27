<?php

use Illuminate\Database\Seeder;

class CurrencyRatesTableSeeder extends Seeder
{
    public static $currency_rates = [
        [ 'currency' => 'CZK', 'rate' => 25.534, 'date' => '2012-11-16'],
        [ 'currency' => 'EUR', 'rate' => 1, 'date' => '2012-11-16'],
        [ 'currency' => 'HUF', 'rate' => 284.18, 'date' => '2012-11-16'],
        [ 'currency' => 'LTL', 'rate' => 3.4528, 'date' => '2012-11-16'],
        [ 'currency' => 'GBP', 'rate' => 0.80245, 'date' => '2012-11-16'],
        [ 'currency' => 'BGN', 'rate' => 1.9558, 'date' => '2012-11-16'],
        [ 'currency' => 'JPY', 'rate' => 103.44, 'date' => '2012-11-16'],
        [ 'currency' => 'ZAR', 'rate' => 11.2927, 'date' => '2012-11-16'],
        [ 'currency' => 'DKK', 'rate' => 7.4585, 'date' => '2012-11-16'],
        [ 'currency' => 'LVL', 'rate' => 0.6963, 'date' => '2012-11-16'],
    ];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach (self::$currency_rates as $currency_rate) {
            DB::table('currency_rates')->insert([
                'currency'       => $currency_rate['currency'],
                'rate'       => $currency_rate['rate'],
                'date'  => $currency_rate['date'],
                'created_at' => date('Y-m-d h:i:s'),
                'updated_at' => date('Y-m-d h:i:s')
            ]);
        }

    }
}
