<?php

namespace Fh\Purchase\Tests\Factories;

use Fh\Purchase\Entities\Customer;
use Fh\Purchase\Entities\Invoice;
use Fh\Purchase\Entities\Order;
use Fh\Purchase\Entities\OrderItem;
use Fh\Purchase\Facades\InvoiceFactoryFacade as InvoiceFactory;
use Fh\Purchase\Tests\Fixtures\People;
use Fh\Purchase\Tests\Fixtures\Product;
use Fh\Purchase\Tests\TestCase;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;

class InvoiceFactoryTest extends TestCase
{
    use RefreshDatabase;

    public function testCreateInvoice()
    {
        $customer = new People;
        $product = new Product;

        $invoice = InvoiceFactory::createInvoice($customer, $product);

        $this->assertDatabaseCount('purchase_invoices', 1);
        $this->assertInstanceOf(Invoice::class, $invoice);

        $this->assertDatabaseCount('purchase_orders', 1);
        $this->assertEquals(1, Invoice::has('order')->count());
        $this->assertInstanceOf(Order::class, $invoice->order);

        $this->assertDatabaseCount('purchase_order_items', 1);
        $this->assertInstanceOf(Collection::class, $invoice->order->items);
        $this->assertInstanceOf(OrderItem::class, $invoice->order->items()->first());

        $this->assertDatabaseCount('purchase_customers', 1);
        $this->assertEquals(1, Invoice::has('customer')->count());
        $this->assertInstanceOf(Customer::class, $invoice->customer);
    }
}
