<?php

use Illuminate\Database\Seeder;

class StatusTableSeeder extends Seeder
{

    public static $statuses = [
        [ 'name' => 'Pending', 'type' => 'tour', 'color' => null, 'sort_order' => 1, 'status' => 0],
        [ 'name' => 'Pending', 'type' => 'task', 'color' => null, 'sort_order' => 1, 'status' => 0],
        [ 'name' => 'Pending', 'type' => 'service_in_tour', 'color' => null, 'sort_order' => 1, 'status' => 0],
        [ 'name' => 'Go Ahead', 'type' => 'tour', 'color' => null, 'sort_order' => 1, 'status' => 0],
        [ 'name' => 'Unconfirmed', 'type' => 'tour', 'color' => null, 'sort_order' => 1, 'status' => 0],
        [ 'name' => 'Cancel', 'type' => 'tour', 'color' => null, 'sort_order' => 1, 'status' => 0],
        [ 'name' => 'Completed', 'type' => 'task', 'color' => null, 'sort_order' => 1, 'status' => 0],
        [ 'name' => 'Abort', 'type' => 'task', 'color' => null, 'sort_order' => 1, 'status' => 0],
        [ 'name' => 'Confirmed', 'type' => 'service_in_tour', 'color' => null, 'sort_order' => 1, 'status' => 0],
        [ 'name' => 'Cancelled', 'type' => 'service_in_tour', 'color' => null, 'sort_order' => 1, 'status' => 0]
    ];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach (self::$statuses as $status) {
            DB::table('status')->insert([
                'name'       => $status['name'],
                'type'       => $status['type'],
                'color'  => $status['color'],
                'sort_order' => $status['sort_order'],
                'status' => $status['status'],
                'created_at' => date('Y-m-d h:i:s'),
                'updated_at' => date('Y-m-d h:i:s')
            ]);
        }

    }
}
