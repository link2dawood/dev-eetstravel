<?php
/**
 * @author: yurapif
 * Date: 09.05.2017
 */

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(\App\Flight::class, function(Faker\Generator $faker){
    return [
        'date_to'       =>  $faker->date(),
        'date_from'     =>  $faker->date(),
        'city_to'      =>  $faker->city,
        'country_from'    =>  $faker->country
    ];
});