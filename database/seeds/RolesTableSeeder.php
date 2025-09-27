<?php

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $names = ['admin', 'user'];
        foreach ($names as $value) {
        	$role = Role::create(['name' => $value]);
        }
    }
}
