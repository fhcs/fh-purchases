<?php

namespace Fh\Purchase\Tests\Casts;

use Fh\Purchase\Casts\ObjectJson;
use Fh\Purchase\Casts\Payment;
use Fh\Purchase\Tests\TestCase;

class ObjectJsonTest extends TestCase
{

    /**
     * @var array
     */
    private $params;

    /**
     * @var Payment
     */
    private $objectJson;

    public function testToArray()
    {
        $this->assertIsArray($this->objectJson->toArray());
        $this->assertEquals($this->params, $this->objectJson->toArray());
    }

    public function testToJson()
    {
        $this->assertJson($this->objectJson->toJson());
        $this->assertEquals(json_encode($this->params), $this->objectJson->toJson());
        $this->assertEquals($this->params, json_decode($this->objectJson->toJson(), true));
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->params = [
            'orderId' => 123,
            'showOrderId' => date_timestamp_get(date_create()),
            'paymentId' => '1234567890',
            'account' => 123,
            'amount' => 100.00,
            'state' => 'end',
            'marketPlace' => 000000000,
            'paymentMethod' => 'ac',
            'stateDate' => '2021-05-18T15:48:32.721+03:00',
            'email' => 'test@test.tt',
            'phone' => '+7(123)456-78-90',
            'details' => 'test',
        ];

        $this->objectJson = new class($this->params) extends ObjectJson {
        };
    }
}
