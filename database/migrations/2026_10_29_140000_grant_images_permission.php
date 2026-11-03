<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

/**
 * /images/index (supplier category images) checks images.index, which did not exist, so the
 * page answered 403 for everyone. Changing those shared images is an admin setting.
 */
class GrantImagesPermission extends Migration
{
    const PERMISSIONS = ['images.index'];

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
