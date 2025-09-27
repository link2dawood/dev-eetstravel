<?php

use Illuminate\Database\Seeder;

class AttachmenttypesTableSeeder extends Seeder
{
    public static $types = [
        ['name' => 'Events',                'model' => 'Event'],
        ['name' => 'Restaurants (Lunch)',   'model' => 'Restaurant'],
        ['name' => 'Restaurants (Dinner)',  'model' => 'Restaurant'],
        ['name' => 'Hotels',                'model' => 'Hotel'],
        ['name' => 'Guides',                'model' => 'Guide'],
        ['name' => 'Flights',               'model' => 'Flight'],
        ['name' => 'Cruises',               'model' => 'Cruise'],
    ];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach (self::$types as $type) {
            DB::table('attachmenttypes')->insert([
                'name'       => $type['name'],
                'model'      => $type['model'],
                'created_at' => date('Y-m-d h:i:s'),
                'updated_at' => date('Y-m-d h:i:s')
            ]);
        }
    }
}
