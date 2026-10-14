<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

/**
 * Communications > Email Templates is shown to everyone with email.main, but no
 * role had any templates.* permission, so the page answered 403 for everybody.
 * Roles with email.main may view templates; the admin role may also manage them.
 */
class GrantEmailTemplatePermissions extends Migration
{
    const VIEW = ['templates.index', 'templates.show'];
    const MANAGE = ['templates.create', 'templates.edit', 'templates.destroy'];

    public function up()
    {
        foreach (array_merge(self::VIEW, self::MANAGE) as $name) {
            Permission::findOrCreate($name, 'web');
        }

        foreach (Role::all() as $role) {
            if ($role->hasPermissionTo('email.main')) {
                $role->givePermissionTo(self::VIEW);
            }
            if ($role->name === 'admin') {
                $role->givePermissionTo(self::MANAGE);
            }
        }

        app()['cache']->forget('spatie.permission.cache');
    }

    public function down()
    {
        foreach (Role::all() as $role) {
            $role->revokePermissionTo(array_filter(array_merge(self::VIEW, self::MANAGE), function ($name) use ($role) {
                return $role->hasPermissionTo($name);
            }));
        }

        app()['cache']->forget('spatie.permission.cache');
    }
}
