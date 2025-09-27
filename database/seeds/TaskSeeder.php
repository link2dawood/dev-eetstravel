<?php

use App\Task;
use Faker\Factory;
use Illuminate\Database\Seeder;

class TaskSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Factory::create();

        Task::truncate();

        foreach (range(1,30) as $i){
            Task::create([
                'status' => 1,
                'content' => $faker->text,
                'start_time' => $faker->dateTime,
                'dead_line' => $faker->dateTime,
                'tour' => $faker->city,
                'assign' => $faker->randomLetter,
                'task_type' => $faker->randomLetter,
            ]);
        }
    }
}
