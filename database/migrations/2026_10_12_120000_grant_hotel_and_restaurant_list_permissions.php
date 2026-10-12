<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class GrantHotelAndRestaurantListPermissions extends Migration
{
    /**
     * Every other service lets a role that can view a record also open the list.
     * hotel.index was never created (so no one saw Hotels in the menu), and the admin
     * role lacked restaurant.index although it can view, create and edit restaurants.
     * Roles that have "<service>.show" get "<service>.index"; nothing else changes.
     */
    private $services = ['hotel', 'restaurant'];

    public function up()
    {
        foreach ($this->services as $service) {
            $index = Permission::firstOrCreate(['name' => "{$service}.index", 'guard_name' => 'web']);
            foreach (Role::where('guard_name', 'web')->get() as $role) {
                if ($role->hasPermissionTo("{$service}.show") && !$role->hasPermissionTo($index)) {
                    $role->givePermissionTo($index);
                }
            }
        }
        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    public function down()
    {
        // hotel.index did not exist before; restaurant.index is only taken back from admin
        Permission::where('name', 'hotel.index')->where('guard_name', 'web')->delete();
        $admin = Role::where('name', 'admin')->where('guard_name', 'web')->first();
        if ($admin && $admin->hasPermissionTo('restaurant.index')) {
            $admin->revokePermissionTo('restaurant.index');
        }
        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
