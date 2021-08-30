<?php

declare(strict_types=1);

namespace Fh\Purchase\Factories;

use Fh\Purchase\Contracts\PayableCustomer;
use Fh\Purchase\Contracts\PayableProduct;
use Fh\Purchase\Entities\Invoice;
use Fh\Purchase\Facades\CustomerFactoryFacade as CustomerFactory;
use Fh\Purchase\Facades\OrderFactoryFacade as OrderFactory;

class InvoiceFactory
{
    /**
     * @param PayableCustomer $customer
     * @param PayableProduct $product
     * @return Invoice
     */
    public function createInvoice(PayableCustomer $customer, PayableProduct $product): Invoice
    {
        $order = OrderFactory::createOrder($product);
        $customer = CustomerFactory::defineCustomer($customer);

        $invoice = Invoice::create([
            'customer_id' => $customer->getId(),
            'order_id' => $order->getId(),
        ]);

        $invoice->order()->associate($order);
        $invoice->customer()->associate($customer);

        return $invoice;
    }
}
