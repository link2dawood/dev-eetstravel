<?php

use Illuminate\Database\Seeder;

class RateTypeSeeder extends Seeder
{
    public static $rate_types = [
        'hotelstart',
        'interate',
        'noterate',
        'servicerate',
        'tourrate',
    ];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach (self::$rate_types as $name) {
            DB::table('rate_types')->insert([
                'name'       => $name,
                'created_at' => date('Y-m-d h:i:s'),
                'updated_at' => date('Y-m-d h:i:s')
            ]);
        }

    }
}
