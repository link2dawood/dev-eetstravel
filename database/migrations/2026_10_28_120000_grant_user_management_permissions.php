<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

/**
 * User Administration is shown to the admin role, and /users now checks the
 * users.* permissions, but the admin role held none of them.
 */
class GrantUserManagementPermissions extends Migration
{
    const PERMISSIONS = ['users.index', 'users.show', 'users.create', 'users.edit', 'users.destroy'];

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
