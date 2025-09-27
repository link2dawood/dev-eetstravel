<?php

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class PermissionNotificationSeeder extends Seeder
{
    public static $permissions = [
        'notification.destroy'         => 'Delete Notifications'
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
