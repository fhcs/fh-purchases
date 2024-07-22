<?php

namespace Fh\Purchase\Tests\Factories;

use Fh\Purchase\Entities\Order;
use Fh\Purchase\Entities\OrderItem;
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
    }

    /**
     * @test
     */
    public function it_can_be_generate_order_from_array(): void
    {
        $products = [
            [
                'name' => 'Test product',
                'price' => 100.00,
                'description' => 'Testing product',
                'type' => 'test_product',
            ],
            [
                'name' => 'Test product 2',
                'price' => 200.00,
                'description' => 'Testing product 2',
                'type' => 'test_product_2',
            ],
        ];

        $order = OrderFactory::generateOrder($products);

        $this->assertDatabaseCount('purchase_orders', 1);
        $this->assertInstanceOf(Order::class, $order);

        $this->assertDatabaseCount('purchase_order_items', 2);
        $this->assertInstanceOf(Collection::class, $order->items);
        $this->assertInstanceOf(OrderItem::class, $order->items()->first());

        $this->assertEquals(2, $order->items->count());
        $this->assertEquals(2, $order->total);
        $this->assertEquals(300, $order->amount);
    }

    /**
     * @test
     */
    public function it_can_be_generate_order_from_array_products(): void
    {
        $products = [
            new Product,
            new Product,
        ];

        $order = OrderFactory::generateOrder($products);

        $this->assertDatabaseCount('purchase_orders', 1);
        $this->assertInstanceOf(Order::class, $order);

        $this->assertDatabaseCount('purchase_order_items', 2);
        $this->assertInstanceOf(Collection::class, $order->items);
        $this->assertInstanceOf(OrderItem::class, $order->items()->first());

        $this->assertEquals(2, $order->items->count());
        $this->assertEquals(2, $order->total);
        $this->assertEquals(200, $order->amount);
    }

    /**
     * @test
     */
    public function it_can_be_generate_order_from_array_one_product(): void
    {
        $products = [
            [
                'name' => 'Test product',
                'price' => 100.00,
                'description' => 'Testing product',
                'type' => 'test_product',
            ]
        ];

        $order = OrderFactory::generateOrder($products);

        $this->assertDatabaseCount('purchase_orders', 1);
        $this->assertInstanceOf(Order::class, $order);

        $this->assertDatabaseCount('purchase_order_items', 1);
        $this->assertInstanceOf(Collection::class, $order->items);
        $this->assertInstanceOf(OrderItem::class, $order->items()->first());

        $this->assertEquals(1, $order->items->count());
        $this->assertEquals($order->items()->first()->quantity, $order->total);
        $this->assertEquals($order->items()->first()->quantity * $order->items()->first()->price, $order->amount);
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

    /**
     * @test
     */
    public function it_can_be_exception_generate_order_if_invalid_data(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $products = [
            [
                'description' => 'Testing product from array attributes',
                'type' => 'test_product',
            ]
        ];

        OrderFactory::generateOrder($products);
    }
}
