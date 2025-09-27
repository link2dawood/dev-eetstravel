<?php

use Illuminate\Database\Seeder;

class AddSettingsEmailFrequency extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\Setting::create([
        	'name'  => 'email_refresh_frequency',
	        'description'   => 'How often sync emails from servers(in minutes)',
	        'value' => 1
        ]);
    }
}
