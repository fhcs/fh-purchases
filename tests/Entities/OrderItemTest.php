<?php

namespace Fh\Purchase\Tests\Entities;

use Fh\Purchase\Entities\OrderItem;
use Fh\Purchase\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class OrderItemTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function it_can_be_create_order_item(): void
    {
        $data = [
            'name' => 'Тестовая услуга',
            'price' => 100.00,
        ];

        $orderItem = factory(OrderItem::class)->create($data);

        $this->assertDatabaseCount('purchase_order_items', 1);
        $this->assertDatabaseHas('purchase_order_items', $data);
        $this->assertEquals('Тестовая услуга', $orderItem->name);
        $this->assertEquals(100.00, $orderItem->price);
        $this->assertNull($orderItem->details);
        $this->assertEquals(1, $orderItem->quantity);
        $this->assertArrayNotHasKey('created_at', $orderItem->toArray());
        $this->assertArrayNotHasKey('updated_at', $orderItem->toArray());
    }

    /**
     * @test
     */
    public function it_can_be_set_order_details(): void
    {
        $data = [
            'name' => 'Тестовая услуга',
            'price' => 100.00,
            'details' => ["type" => "test", "name" => "Тестовая услуга", "price" => "100"]
        ];

        $orderItem = factory(OrderItem::class)->create($data);

        $this->assertNotNull($orderItem->details);
        $this->assertIsArray($orderItem->details);
    }
}
