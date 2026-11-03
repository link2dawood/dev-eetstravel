<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

/**
 * /permissions now checks permissions.* permissions, which did not exist (the
 * pages were open even to guests). Only the admin role may manage permissions.
 */
class GrantPermissionManagementPermissions extends Migration
{
    const PERMISSIONS = ['permissions.index', 'permissions.show', 'permissions.create', 'permissions.edit', 'permissions.destroy'];

    public function up()
    {
        foreach (self::PERMISSIONS as $name) {
            Permission::findOrCreate($name, 'web');
        }

        $admin = Role::where('name', 'admin')->where('guard_name', 'web')->first();
        if ($admin) {
            $admin->givePermissionTo(self::PERMISSIONS);
        }

        app()['cache']->forget('spatie.permission.cache');
    }

    public function down()
    {
        $admin = Role::where('name', 'admin')->where('guard_name', 'web')->first();
        if ($admin) {
            $admin->revokePermissionTo(array_filter(self::PERMISSIONS, function ($name) use ($admin) {
                return $admin->hasPermissionTo($name);
            }));
        }

        app()['cache']->forget('spatie.permission.cache');
    }
}
