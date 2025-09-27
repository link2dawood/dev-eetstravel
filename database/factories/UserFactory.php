<?php
/**
* @author yurapif
*/

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(\App\User::class, function(Faker\Generator $faker){
    return [
        'name' => $faker->name,
        'email' => $faker->email,
        'password' => bcrypt('123456')
    ];
});