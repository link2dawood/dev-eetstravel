<?php

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class PermissionHolidaySeeder extends Seeder
{
    public static $permissions = [
        'holiday.index' => 'Holiday List',
        'holiday.edit' => 'Holiday Edit',
        'holiday.show' => 'Holiday Show',
        'holiday.create' => 'Holiday Create',
        'holiday.destroy' => 'Holiday Delete',
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

            if($role && $name == 'holiday.destroy') {
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
