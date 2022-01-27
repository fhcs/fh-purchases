<?php

/** @var Factory $factory */

use Faker\Generator as Faker;
use Fh\Purchase\Entities\Customer;
use Fh\Purchase\Entities\Invoice;
use Fh\Purchase\Entities\Order;
use Illuminate\Database\Eloquent\Factory;

$factory->define(Invoice::class, function (Faker $faker) {
    return [
        'target' => $faker->sentence(2),
        'customer_id' => \factory(Customer::class)->create(),
        'order_id' => \factory(Order::class)->create()
    ];
});