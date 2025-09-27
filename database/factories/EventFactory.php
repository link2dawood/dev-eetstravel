<?php
/**
 * @author: yurapif
 * Date: 05.05.2017
 */

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(\App\Event::class, function(\Faker\Generator $faker) {

    return [
        'name'              =>  $faker->company,
        'address_first'     =>  $faker->address,
        'address_second'    =>  $faker->address,
        'city'              =>  $faker->city,
        'code'              =>  $faker->numberBetween(1, 50),
        'country'           =>  $faker->country,
        'work_phone'        =>  $faker->phoneNumber,
        'work_fax'          =>  $faker->phoneNumber,
        'work_email'        =>  $faker->email,
        'contact_name'      =>  $faker->name,
        'contact_phone'     =>  $faker->phoneNumber,
        'contact_email'     =>  $faker->email,
        'comments'          =>  $faker->text,
        'int_comments'      =>  $faker->text,
        'website'           =>  $faker->url
    ];
});