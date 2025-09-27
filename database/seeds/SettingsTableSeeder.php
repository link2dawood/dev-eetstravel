<?php

use Illuminate\Database\Seeder;
use App\Setting;

/**
* settings seeder
*/
class SettingsTableSeeder extends Seeder
{
	public function run()
	{
	    // Tour Service Notification
		Setting::query()->create([
			'name' => 'tour_service_notification',
			'description' => 'Notificate users about pending service at tour (value in days)',
			'value' => '15']);


		// Task Deadline Notification
        Setting::query()->create([
            'name' => 'task_deadline_notification',
            'description' => 'Notificated users about task deadline (value in days)',
            'value' => '10']);
	}
}