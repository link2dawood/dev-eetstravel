<?php

use Illuminate\Database\Seeder;

class CriteriaTableSeeder extends Seeder
{
    public static $criterias = [
        [ 'name' => 'Air Conditioning', 'short_name' => 'AirCon', 'icon' => 'air_conditioning', 'criteria_type' => null],
        [ 'name' => 'Conference Rooms', 'short_name' => 'Conf-Room', 'icon' => 'conference', 'criteria_type' => null],
        [ 'name' => 'Internet', 'short_name' => 'Internet', 'icon' => 'internet', 'criteria_type' => null],
        [ 'name' => 'Parking', 'short_name' => 'Parking', 'icon' => 'parking', 'criteria_type' => null],
        [ 'name' => 'Spa Treatments', 'short_name' => 'Spa', 'icon' => '', 'criteria_type' => null],
        [ 'name' => 'Swimming Pool', 'short_name' => 'Swim', 'icon' => 'swimming_pool', 'criteria_type' => null]
    ];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach (self::$criterias as $criteria) {
            DB::table('criterias')->insert([
                'name'       => $criteria['name'],
                'short_name'       => $criteria['short_name'],
                'icon'  => $criteria['icon'],
                'criteria_type' => $criteria['criteria_type'],
                'created_at' => date('Y-m-d h:i:s'),
                'updated_at' => date('Y-m-d h:i:s')
            ]);
        }

    }
}
