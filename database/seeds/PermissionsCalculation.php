<?php

use Illuminate\Database\Seeder;

class PermissionsCalculation extends Seeder
{
	public static $permissions = [
		'quotation.calculation'        =>   'Calculation',
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
