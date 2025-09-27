<?php

use Illuminate\Database\Seeder;

class CriteriaTypeSeeder extends Seeder
{
    public static $criteria_types = [
        ['name' => 'hotel', 'service_type' => 'hotel'],
        ['name' => 'event', 'service_type' => 'event'],
        ['name' => 'restaurant', 'service_type' => 'restaurant'],
        ['name' => 'guide', 'service_type' => 'guide'],
        ['name' => 'transfer', 'service_type' => 'transfer'],
        ['name' => 'flight', 'service_type' => 'flight'],
        ['name' => 'cruise', 'service_type' => 'cruise']
    ];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach (self::$criteria_types as $criteria_type) {
            DB::table('criteria_types')->insert([
                'name'       => $criteria_type['name'],
                'service_type'       => $criteria_type['service_type'],
                'created_at' => date('Y-m-d h:i:s'),
                'updated_at' => date('Y-m-d h:i:s')
            ]);
        }

    }
}
