<?php

use Illuminate\Database\Seeder;

class RateTableSeeder extends Seeder
{
    public static $rates = [
        [ 'name' => '1*', 'mark' => '1', 'rate_type' => null, 'sort_order' => 1],
        [ 'name' => '2**', 'mark' => '2', 'rate_type' => null, 'sort_order' => 1],
        [ 'name' => '2**+', 'mark' => '3', 'rate_type' => null, 'sort_order' => 1],
        [ 'name' => '3***', 'mark' => '4', 'rate_type' => null, 'sort_order' => 1],
        [ 'name' => '3***+', 'mark' => '5', 'rate_type' => null, 'sort_order' => 1],
        [ 'name' => '4****', 'mark' => '6', 'rate_type' => null, 'sort_order' => 1],
        [ 'name' => '4****+', 'mark' => '7', 'rate_type' => null, 'sort_order' => 1],
        [ 'name' => '5*****', 'mark' => '8', 'rate_type' => null, 'sort_order' => 1],
        [ 'name' => '5*****+', 'mark' => '9', 'rate_type' => null, 'sort_order' => 1]
    ];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach (self::$rates as $rate) {
            DB::table('rates')->insert([
                'name'       => $rate['name'],
                'mark'       => $rate['mark'],
                'rate_type'  => $rate['rate_type'],
                'sort_order' => $rate['sort_order'],
                'created_at' => date('Y-m-d h:i:s'),
                'updated_at' => date('Y-m-d h:i:s')
            ]);
        }

    }
}
