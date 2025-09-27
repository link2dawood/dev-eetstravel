<?php
/**
 * @author: yurapif
 * Date: 19.05.2017
 */

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(\App\Cruises::class, function(Faker\Generator $faker){
    return [
        'name' => $faker->city
    ];
});