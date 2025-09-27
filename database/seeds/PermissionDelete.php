<?php

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class PermissionDelete extends Seeder
{
    public static $permissions = [
        'user.destroy'        =>   'Delete Users',
        'role.destroy'           =>   'Delete Roles',
        'permission.destroy'  =>   'Delete Permissions',
        'tour.destroy'           =>   'Delete Tours',
        'task.destroy'     =>   'Delete Tasks',
        'event.destroy'     =>   'Delete Events',
        'restaurant.destroy'     =>   'Delete Restaurants',
        'hotel.destroy'     =>   'Delete Hotels',
        'guide.destroy'     =>   'Delete Guides',
        'flight.destroy'     =>   'Delete Flights',
        'cruise.destroy'     =>   'Delete Cruises',
        'transfer.destroy'     =>   'Delete Transfer',
        'bus.destroy'     =>   'Delete Buses',
        'driver.destroy'     =>   'Delete Drivers',
        'status.destroy'     =>   'Delete Statuses',
        'room_types.destroy'     =>   'Delete Room Types',
        'rate.destroy'     =>   'Delete Rates',
        'currency_rate.destroy'     =>   'Delete Currency Rate',
        'currencies.destroy'     =>   'Delete Currencies',
        'criteria.destroy'     =>   'Delete Criteria',
        'comment.destroy'     =>   'Delete Comments',
        'client.destroy'     =>   'Delete Clients',
        'tour_package.destroy'     =>   'Delete Tour Packages',
        'delete_agreements'     =>   'Delete Agreements',
        'menu.destroy_menu'     =>   'Delete Menus',
        'delete_season'     =>   'Delete Season Prices',
        'announcement.destroy'     =>   'Delete Announcements',
    ];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $role = Role::query()->where('name', 'admin')->first();

        foreach (self::$permissions as $name => $alias) {
            $id_permission = DB::table('permissions')->insertGetId([
                'name'       => $name,
                'alias'      => $alias,
                'created_at' => date('Y-m-d h:i:s'),
                'updated_at' => date('Y-m-d h:i:s')
            ]);

            if($role) {
                DB::table('role_has_permissions')->insert(
                    [
                        'permission_id' => $id_permission,
                        'role_id' => $role->id
                    ]
                );
            }
        }
    }
}
