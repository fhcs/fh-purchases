<?php

namespace Fh\Purchase\Tests\Factories;

use Fh\Purchase\Entities\Order;
use Fh\Purchase\Entities\OrderItem;
use Fh\Purchase\Enums\OrderStatus;
use Fh\Purchase\Facades\OrderFactoryFacade as OrderFactory;
use Fh\Purchase\Tests\Fixtures\Product;
use Fh\Purchase\Tests\TestCase;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;

class OrderFactoryTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function it_can_be_create_order_from_payable_product(): void
    {
        $product = new Product();

        $order = OrderFactory::createOrder($product);

        $this->assertDatabaseCount('purchase_orders', 1);
        $this->assertInstanceOf(Order::class, $order);

        $this->assertDatabaseCount('purchase_order_items', 1);
        $this->assertInstanceOf(Collection::class, $order->items);
        $this->assertInstanceOf(OrderItem::class, $order->items()->first());

        $this->assertEquals(1, $order->items->count());
        $this->assertEquals($order->items()->first()->quantity, $order->total);
        $this->assertEquals($order->items()->first()->quantity * $order->items()->first()->price, $order->amount);
        $this->assertEquals(OrderStatus::NEW, $order->status);
    }

    /**
     * @test
     */
    public function it_can_be_create_order_from_array(): void
    {
        $product = [
            'name' => 'Test product',
            'price' => 100.00,
            'description' => 'Testing product from array attributes',
            'type' => 'test_product',
        ];

        $order = OrderFactory::createOrder($product);

        $this->assertDatabaseCount('purchase_orders', 1);
        $this->assertInstanceOf(Order::class, $order);

        $this->assertDatabaseCount('purchase_order_items', 1);
        $this->assertInstanceOf(Collection::class, $order->items);
        $this->assertInstanceOf(OrderItem::class, $order->items()->first());

        $this->assertEquals(1, $order->items->count());
        $this->assertEquals($order->items()->first()->quantity, $order->total);
        $this->assertEquals($order->items()->first()->quantity * $order->items()->first()->price, $order->amount);
        $this->assertEquals(OrderStatus::NEW, $order->status);
    }

    /**
     * @test
     */
    public function it_can_be_exception_create_order_if_invalid_data(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $product = [
            'description' => 'Testing product from array attributes',
            'type' => 'test_product',
        ];

        OrderFactory::createOrder($product);
    }
}
