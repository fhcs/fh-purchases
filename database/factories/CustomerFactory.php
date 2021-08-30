<?php

use Faker\Generator as Faker;
use Fh\Purchase\Entities\Customer;
use Illuminate\Database\Eloquent\Factory;
use Illuminate\Support\Str;

/** @var Factory $factory */
$factory->define(Customer::class, function (Faker $faker) {
    $phone = $faker->e164PhoneNumber;

    return [
        'account' => Str::substr($phone, 2),
        'email' => $faker->email,
        'phone' => $phone,
    ];
});
