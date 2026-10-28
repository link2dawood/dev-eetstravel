<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

/**
 * /roles now checks roles.* permissions, which did not exist. Only the admin
 * role (which already sees User Administration) may manage roles.
 */
class GrantRoleManagementPermissions extends Migration
{
    const PERMISSIONS = ['roles.index', 'roles.show', 'roles.create', 'roles.edit', 'roles.destroy'];

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
