<?php
/**
 * @author: yurapif
 * Date: 05.05.2017
 */

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(\App\Hotel::class, function(Faker\Generator $faker){

    return [
        'name'              =>  $faker->name,
        'address_first'     =>  $faker->address,
        'address_second'    =>  $faker->address,
        'city'              =>  $faker->city,
        'code'              =>  $faker->randomDigitNotNull,
        'country'           =>  $faker->numberBetween(1, 10),
        'work_phone'        =>  $faker->phoneNumber,
        'work_fax'          =>  $faker->phoneNumber,
        'work_email'        =>  $faker->companyEmail,
        'contact_name'      =>  $faker->name,
        'contact_phone'     =>  $faker->phoneNumber,
        'contact_email'     =>  $faker->email,
        'comments'          =>  $faker->text,
        'int_comments'      =>  $faker->text,
        'note'              =>  $faker->text,
        'category'          =>  $faker->randomDigitNotNull
    ];
});