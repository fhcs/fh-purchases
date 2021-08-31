<?php

namespace Fh\Purchase\Tests;

use Fh\Purchase\Entities\Invoice;
use Fh\Purchase\Events\InvoiceCreated;
use Fh\Purchase\Facades\PurchaseFacade as Purchase;
use Fh\Purchase\Tests\Fixtures\People;
use Fh\Purchase\Tests\Fixtures\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;

class PurchaseTest extends TestCase
{
    use RefreshDatabase;

    public function testCreateInvoice()
    {
        Event::fake([InvoiceCreated::class]);

        $customer = new People;
        $product = new Product;

        $invoice = Purchase::createInvoice($customer, $product);

        $this->assertInstanceOf(Invoice::class, $invoice);
        Event::assertDispatched(InvoiceCreated::class, function (InvoiceCreated $event) use ($invoice) {
            return $event->invoice->order_id === $invoice->getOrderId();
        });
    }
}
