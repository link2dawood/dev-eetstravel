<?php

use Illuminate\Database\Seeder;

class DriverPermissionsSeeder extends Seeder
{

	public static $permissions = [
		'driver.index'         => 'Driver List',
		'driver.create'        => 'Driver Create',
		'driver.edit'          => 'Driver Edit',
		'driver.show'          => 'Driver Show'
	];

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		foreach (self::$permissions as $name => $alias) {
			DB::table('permissions')->insert([
				'name'       => $name,
				'alias'      => $alias,
				'created_at' => date('Y-m-d h:i:s'),
				'updated_at' => date('Y-m-d h:i:s')
			]);
		}

	}
}