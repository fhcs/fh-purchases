<?php

namespace Fh\Purchase\Tests\Enums;

use Fh\Purchase\Enums\OrderStatus;
use Fh\Purchase\Tests\TestCase;

class OrderStatusTest extends TestCase
{
    /**
     * @test
     */
    public function it_can_be_get_status(): void
    {
        $this->assertEquals(OrderStatus::CLOSED, 'закрыт');
        $this->assertEquals(OrderStatus::END, OrderStatus::status('end'));
        $this->assertEquals('test', OrderStatus::status('test'));
    }
}
