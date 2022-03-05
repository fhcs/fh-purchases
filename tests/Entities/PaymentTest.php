<?php

namespace Fh\Purchase\Tests\Entities;

use Fh\Purchase\Entities\Payment;
use Fh\Purchase\Enums\PaymentStatus;
use Fh\Purchase\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Str;

class PaymentTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * @var Payment
     */
    private $payment;

    /**
     * @var array
     */
    private $paymentData;

    /**
     * @test
     */
    public function testUpdateOrInsert(): void
    {
        $payment = Payment::updateOrInsert($this->paymentData, self::PAYMENT_SYSTEM);

        $this->assertInstanceOf(Payment::class, $payment);
        $this->assertDatabaseHas('purchase_payments', [
            'id' => $this->paymentData['paymentId'],
            'system' => self::PAYMENT_SYSTEM,
            'status' => PaymentStatus::status($this->paymentData['state']),
            'context' => json_encode($this->paymentData)
        ]);
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->payment = factory(Payment::class)->create();

        $paymentId = $this->faker->randomNumber(9);
        $phone = $this->faker->e164PhoneNumber;

        $this->paymentData = [
            'orderId' => $this->faker->uuid,
            'showOrderId' => date_timestamp_get(date_create()),
            'paymentId' => $paymentId,
            'account' => Str::substr($phone, 2),
            'amount' => $this->faker->randomNumber(4),
            'state' => 'end',
            'marketPlace' => $this->faker->randomNumber(7),
            'paymentMethod' => 'ac',
            'stateDate' => $this->faker->dateTime()->format('Y-m-d\TH:i:sP'),
            'email' => $this->faker->email,
            'phone' => $phone,
            'details' => $this->faker->sentence,
        ];
    }
}
