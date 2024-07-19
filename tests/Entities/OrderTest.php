<?php

namespace Fh\Purchase\Tests\Entities;

use Fh\Purchase\Entities\Order;
use Fh\Purchase\Entities\OrderItem;
use Fh\Purchase\Tests\TestCase;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Ramsey\Uuid\Uuid;

class OrderTest extends TestCase
{
    use RefreshDatabase;

    public function testCreate()
    {
        $order = Order::create();

        $this->assertInstanceOf(Order::class, $order);
        $this->assertDatabaseCount('purchase_orders', 1);
        $this->assertDatabaseHas('purchase_orders', [
            'total' => 0,
            'amount' => 0.00,
        ]);
        $this->assertArrayNotHasKey('created_at', $order->toArray());
        $this->assertArrayNotHasKey('updated_at', $order->toArray());
    }

    public function testSetCreatedAtAttribute()
    {
        $order = Order::create();
        $this->assertIsString($order->uuid);
        $this->assertTrue(Uuid::isValid($order->uuid));
    }

    public function testAddOrderItem()
    {
        $order = Order::create();
        $product1 = [
            'name' => 'Тестовая услуга',
            'price' => 100.00,
            'quantity' => 2,
            'details' => ["type" => "test", "name" => "Тестовая услуга", "price" => "100"]
        ];
        $product2 = [
            'name' => 'Тестовая услуга 2',
            'price' => 200.00,
            'quantity' => 1,
            'details' => ["type" => "test", "name" => "Тестовая услуга 2", "price" => "200"]
        ];
        $order->addOrderItem(OrderItem::create($product1));
        $order->addOrderItem(OrderItem::create($product2));

        $this->assertDatabaseCount('purchase_order_items', 2);
        $this->assertInstanceOf(Collection::class, $order->items);
        $this->assertInstanceOf(OrderItem::class, $order->items()->first());

        $this->assertEquals(2, $order->items->count());
        $this->assertEquals($product1['quantity'] + $product2['quantity'], $order->total);
        $this->assertEquals(($product1['quantity'] * $product1['price']) + ($product2['quantity'] * $product2['price']), $order->amount);
    }
}
