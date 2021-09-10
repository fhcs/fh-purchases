<?php

namespace Fh\Purchase\Tests;

use Fh\Purchase\Entities\Invoice;
use Fh\Purchase\Events\InvoiceCreated;
use Fh\Purchase\Facades\Purchase;
use Fh\Purchase\Tests\Fixtures\People;
use Fh\Purchase\Tests\Fixtures\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Event;

class PurchaseTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @var People
     */
    private $customer;

    /**
     * @var Product
     */
    private $product;

    public function testCreateInvoice()
    {
        Event::fake([InvoiceCreated::class]);

        $invoice = Purchase::createInvoice($this->customer, $this->product);

        $this->assertDatabaseCount('purchase_invoices', 1);
        $this->assertInstanceOf(Invoice::class, $invoice);
        Event::assertDispatched(InvoiceCreated::class, function (InvoiceCreated $event) use ($invoice) {
            return $event->invoice->order_id === $invoice->getOrderId();
        });
    }

    /**
     * @test
     */
    public function it_can_be_get_invoices(): void
    {
        $invoice = Purchase::createInvoice($this->customer, $this->product);

        $this->assertInstanceOf(Collection::class, Purchase::invoices());
        $this->assertInstanceOf(Invoice::class, Purchase::invoices()->first());
        $this->assertInstanceOf(Invoice::class, Purchase::invoices($invoice->getOrderId()));
        $this->assertEquals($invoice->getOrderId(), Purchase::invoices($invoice->getOrderId())->getOrderId());
        $this->assertNull(Purchase::invoices('invalid-order-id'));
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->customer = new People;
        $this->product = new Product;
    }
}
