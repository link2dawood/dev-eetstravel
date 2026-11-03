<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

/**
 * The settings.* permissions existed but no role held them, so /settings
 * answered 403 for everyone. Only the admin role may manage settings.
 * settings.show is kept for existing assignments; the show page was removed.
 */
class GrantSettingsPermissions extends Migration
{
    const PERMISSIONS = ['settings.index', 'settings.create', 'settings.edit', 'settings.destroy'];

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
