<?php

use Illuminate\Database\Seeder;
use App\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

/**
 * Class AdminUserSeeder
 */
class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $email = config('admin.seed_email');
        $password = config('admin.seed_password');

        if (!is_string($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)
            || !is_string($password) || strlen($password) < 16) {
            throw new RuntimeException('Set ADMIN_SEED_EMAIL and ADMIN_SEED_PASSWORD (at least 16 characters) before seeding.');
        }

        $admin = User::create([
            'name' => 'admin',
            'email' => $email,
            'password' => bcrypt($password) ]);
        $role = Role::findOrFail(1);
        $admin->roles()->save($role);
        $permissions = Permission::all();
        foreach ($permissions as $permission) {
            $role->givePermissionTo($permission);
        }
    }
}
