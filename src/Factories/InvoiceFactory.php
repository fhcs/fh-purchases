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
     * @param string $target
     * @return Invoice
     */
    public function createInvoice(PayableCustomer $customer, PayableProduct $product, string $target = ''): Invoice
    {
        $order = OrderFactory::createOrder($product);
        $customer = CustomerFactory::defineCustomer($customer);

        $invoice = Invoice::create([
            'target' => $target,
            'customer_id' => $customer->getId(),
            'order_id' => $order->getId(),
        ]);

        $invoice->order()->associate($order);
        $invoice->customer()->associate($customer);

        return $invoice;
    }

    /**
     * @param PayableCustomer $customer
     * @param array $products
     * @param string $target
     *
     * @return Invoice
     */
    public function generateInvoice(PayableCustomer $customer, array $products, string $target = ''): Invoice
    {
        $order = OrderFactory::generateOrder($products);
        $customer = CustomerFactory::defineCustomer($customer);

        $invoice = Invoice::create([
            'target' => $target,
            'customer_id' => $customer->getId(),
            'order_id' => $order->getId(),
        ]);

        $invoice->order()->associate($order);
        $invoice->customer()->associate($customer);

        return $invoice;
    }
}
