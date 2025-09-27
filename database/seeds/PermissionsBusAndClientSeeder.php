<?php

use Illuminate\Database\Seeder;

class PermissionsBusAndClientSeeder extends Seeder
{
    public static $permissions = [
        'bus.index'         => 'Buses List',
        'bus.create'        => 'Bus Create',
        'bus.edit'          => 'Bus Edit',
        'bus.show'          => 'Bus Show',
        'bus_calendar'          => 'Bus Calendar',
        'clients.index'          => 'Clients List',
        'clients.create'          => 'Client Create',
        'clients.edit'          => 'Client Edit',
        'clients.show'          => 'Client Show'
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
