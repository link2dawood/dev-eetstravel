<?php

use Illuminate\Database\Seeder;
use App\Chat;

class MainChatTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Chat::create([
        	'title' => 'Main Chat',
        	'type' => 1,
        	'author' => 1]);
    }
}
