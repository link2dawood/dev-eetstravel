<?php

use Illuminate\Database\Seeder;

class PermissionsAnnouncements extends Seeder
{
    public static $permissions = [
        'announcements.index'        =>   'Announcements List',
        'announcements.create'        =>   'Announcements Create',
        'announcements.edit'        =>   'Announcements Edit',
        'announcements.show'        =>   'Announcements Show',
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
