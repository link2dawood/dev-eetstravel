<?php

use Illuminate\Database\Seeder;

class PermissionsDashboard extends Seeder
{
    public static $permissions = [
        'dashboard.calendar'        =>   'Dashboard Tasks Calendar',
        'dashboard.inbox'           =>   'Dashboard Inbox',
        'dashboard.tours_calendar'  =>   'Dashboard Tours Calendar',
        'dashboard.announcements'   =>   'Dashboard Announcements',
        'dashboard.tasks'           =>   'Dashboard Tasks',
        'dashboard.latest_tours'    =>   'Dashboard Latest Tours',
        'dashboard.chat_groups'     =>   'Dashboard Chat groups',
        'dashboard.main_chat'       =>   'Dashboard Main Chat',
        'dashboard.activities'      =>   'Dashboard Activities'
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
