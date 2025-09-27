<?php

use Illuminate\Database\Seeder;

class RoomTypesTableSeeder extends Seeder
{
    public static $room_types = [
        [ 'code' => 'SIN', 'name' => 'Single', 'sort_order' => 1],
        [ 'code' => 'DOU', 'name' => 'Double', 'sort_order' => 2],
        [ 'code' => 'TRI', 'name' => 'Triple', 'sort_order' => 3],
        [ 'code' => 'DFS', 'name' => 'Double for single', 'sort_order' => 1],
        [ 'code' => 'SIE', 'name' => 'Suite', 'sort_order' => 3],
        [ 'code' => 'TWN', 'name' => 'Twin', 'sort_order' => 3]
    ];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach (self::$room_types as $room_type) {
            DB::table('room_types')->insert([
                'code'       => $room_type['code'],
                'name'       => $room_type['name'],
                'sort_order'  => $room_type['sort_order'],
                'created_at' => date('Y-m-d h:i:s'),
                'updated_at' => date('Y-m-d h:i:s')
            ]);
        }

    }
}
