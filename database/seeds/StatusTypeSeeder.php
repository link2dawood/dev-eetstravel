<?php

use Illuminate\Database\Seeder;

class StatusTypeSeeder extends Seeder
{
    public static $status_types = [
        ['name' => 'Tour', 'type' => 'tour'],
        ['name' => 'Task', 'type' => 'task'],
        ['name' => 'Service In Tour', 'type' => 'service_in_tour'],
        ['name' => 'Hotel', 'type' => 'hotel'],
        ['name' => 'Bus', 'type' => 'bus']
    ];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach (self::$status_types as $status_type) {
            DB::table('status_types')->insert([
                'name'       => $status_type['name'],
                'type'       => $status_type['type'],
                'created_at' => date('Y-m-d h:i:s'),
                'updated_at' => date('Y-m-d h:i:s')
            ]);
        }

    }
}
