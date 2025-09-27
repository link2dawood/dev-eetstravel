<?php

use Illuminate\Database\Seeder;

class AdditionalPermissions extends Seeder
{
    public static $permissions = [
        'quotation.edit'  =>   'Quotation Edit',
        'quotation.add'  =>   'Quotation Create',

        'comparison.show'  =>   'Front Sheet Show',

        'menu.create'  =>   'Menu Create',
        'menu.edit'  =>   'Menu Edit',
        'menu.show'  =>   'Menu Show',

        'create_season'  =>   'Create Season Price',
        'edit_season'  =>   'Edit Season Price',

        'create_agreements'  =>   'Create Agreements',
        'edit_agreements'  =>   'Edit Agreements',

        'guestlist.index'  =>   'List Guests',
        'guestList.add'  =>   'Create Guest List',
        'guestList.showbyid'  =>   'Show Guest List',
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
