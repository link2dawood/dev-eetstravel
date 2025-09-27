<?php

use Illuminate\Database\Seeder;

class PermissionsChatAndSuplierSearchSeeder extends Seeder
{
    public static $permissions = [
        'supplier_search'         => 'Supplier Search',
        'chat.index'        => 'Chat Index',
        'chat.main'        => 'Chat Main',
        'chat.create'          => 'Chat Create',
        'chat.show'          => 'Chat Show',
        'chat.edit'          => 'Chat Edit'
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
