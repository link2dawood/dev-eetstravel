<?php

use Illuminate\Database\Seeder;

class PermissionsSeeder extends Seeder
{

    public static $permissions = [
        'hotel.index'           =>  'Hotel List',
        'hotel.create'          =>  'Hotel Create',
        'hotel.edit'            =>  'Hotel Edit',
        'hotel.show'            =>  'Hotel Show',
        'event.index'           =>  'Event List',
        'event.create'          =>  'Event Create',
        'event.edit'            =>  'Event Edit',
        'event.show'            =>  'Event Show',
        'restaurant.index'      =>  'Restaurant List',
        'restaurant.create'     =>  'Restaurant Create',
        'restaurant.edit'       =>  'Restaurant Edit',
        'restaurant.show'       =>  'Restaurant Show',
        'guide.index'           =>  'Guide List',
        'guide.create'          =>  'Guide Create',
        'guide.edit'            =>  'Guide Edit',
        'guide.show'            =>  'Guide Show',
        'transfer.index'        =>  'Transfer List',
        'transfer.create'       =>  'Transfer Create',
        'transfer.edit'         =>  'Transfer Edit',
        'transfer.show'         =>  'Transfer Show',
        'tour.index'            =>  'Tour List',
        'tour.create'           =>  'Tour Create',
        'tour.edit'             =>  'Tour Edit',
        'tour.show'             =>  'Tour Show',
        'tour_package.index'    =>  'Tour Package List',
        'tour_package.create'   =>  'Tour Package Create',
        'tour_package.edit'     =>  'Tour Package Edit',
        'tour_package.show'     =>  'Tour Package Show',
        'task.index'            =>  'Task List',
        'task.create'           =>  'Task Create',
        'task.edit'             =>  'Task Edit',
        'task.show'             =>  'Task Show',
        'comment.index'         =>  'Comment List',
        'comment.create'        =>  'Comment Create',
        'comment.edit'          =>  'Comment Edit',
        'comment.show'          =>  'Comment Show',

        'status.index'          =>  'Status List',
        'status.create'         =>  'Status Create',
        'status.edit'           =>  'Status Edit',
        'status.show'           =>  'Status Show',

        'room_types.index'      =>  'Room Types List',
        'room_types.create'     =>  'Room Type Create',
        'room_types.edit'       =>  'Room Type Edit',
        'room_types.show'       =>  'Room Type Show',

        'rate.index'            =>  'Rates List',
        'rate.create'           =>  'Rate Create',
        'rate.edit'             =>  'Rate Edit',
        'rate.show'             =>  'Rate Show',

        'currency_rate.index'   =>  'Currency Rate List',
        'currency_rate.create'  =>  'Currency Rate Create',
        'currency_rate.edit'    =>  'Currency Rate Edit',
        'currency_rate.show'    =>  'Currency Rate Show',

        'currencies.index'      =>  'Currencies List',
        'currencies.create'     =>  'Currencies Create',
        'currencies.edit'       =>  'Currencies Edit',
        'currencies.show'       =>  'Currencies Show',

        'criteria.index'        =>  'Criteria List',
        'criteria.create'       =>  'Criteria Create',
        'criteria.edit'         =>  'Criteria Edit',
        'criteria.show'         =>  'Criteria Show',
        'cruises.index'         => 'Cruise List',
        'cruises.create'        => 'Cruise Create',
        'cruises.edit'          => 'Cruise Edit',
        'cruises.show'          => 'Cruise Show',
        'flights.index'         => 'Flight List',
        'flights.create'        => 'Flight Create',
        'flights.edit'          => 'Flight Edit',
        'flights.show'          => 'Flight Show',
    ];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach (self::$permissions as $name => $alias) {
            DB::table('permissions')->insert([
                'name'       => $name,
                'alias'      => $alias,
                'created_at' => date('Y-m-d h:i:s'),
                'updated_at' => date('Y-m-d h:i:s')
            ]);
        }

    }
}
