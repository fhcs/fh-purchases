<?php

/** @var Factory $factory */

use Faker\Generator as Faker;
use Fh\Purchase\Entities\Payment;
use Illuminate\Database\Eloquent\Factory;
use Illuminate\Support\Str;

$factory->define(Payment::class, function (Faker $faker) {

    $paymentId = $faker->randomNumber();
    $phone = $faker->e164PhoneNumber;

    $paymentData = [
        'orderId' => $faker->uuid,
        'showOrderId' => date_timestamp_get(date_create()),
        'paymentId' => $paymentId,
        'account' => Str::substr($phone, 2),
        'amount' => $faker->randomNumber(3),
        'state' => 'end',
        'marketPlace' => $faker->randomNumber(),
        'paymentMethod' => 'ac',
        'stateDate' => $faker->dateTime(),
        'email' => $faker->email,
        'phone' => $phone,
        'details' => 'test',
    ];

    return [
        'id' => $paymentId,
        'system' => $faker->word,
        'status' => $faker->word,
        'context' => $paymentData
    ];
});