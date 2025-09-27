<?php

use Illuminate\Database\Seeder;

class PermissionsFilghtCruiseSeeder extends Seeder
{

    public static $permissions = [
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
