<?php

use Faker\Generator as Faker;
use Fh\Purchase\Entities\Order;
use Illuminate\Database\Eloquent\Factory;

/** @var Factory $factory */
$factory->define(Order::class, function (Faker $faker) {
    return [
        'total' => 0,
        'amount' => 0.00,
    ];
});
