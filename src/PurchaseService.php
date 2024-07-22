<?php

declare(strict_types=1);

namespace Fh\Purchase;

use Fh\Purchase\Contracts\PayableCustomer;
use Fh\Purchase\Contracts\PayableProduct;
use Fh\Purchase\Entities\Invoice;
use Fh\Purchase\Events\InvoiceCreated;
use Fh\Purchase\Facades\InvoiceFactoryFacade as InvoiceFactory;
use Illuminate\Support\Collection;

class PurchaseService
{
    /**
     * Создает счет на оплату
     *
     * @param PayableCustomer $customer
     * @param PayableProduct $product
     * @param string $target
     * @return Invoice
     */
    public function createInvoice(PayableCustomer $customer, PayableProduct $product, string $target = ''): Invoice
    {
        return tap(InvoiceFactory::createInvoice($customer, $product, $target), function ($invoice) {
            event(new InvoiceCreated($invoice));
        });
    }

    /**
     * Генерирует счет на оплату
     *
     * @param PayableCustomer $customer
     * @param array $products
     * @param string $target
     * @return Invoice
     */
    public function generateInvoice(PayableCustomer $customer, array $products, string $target = ''): Invoice
    {
        return tap(InvoiceFactory::generateInvoice($customer, $products, $target), function ($invoice) {
            event(new InvoiceCreated($invoice));
        });
    }

    /**
     * @param string $byOrderId
     * @return Collection|Invoice|null
     */
    public function invoices(string $byOrderId = '')
    {
        $result = Invoice::cursor();

        if ($byOrderId) {
            return $result->first(function (Invoice $invoice) use ($byOrderId) {
                return $invoice->getOrderId() === $byOrderId;
            });
        }

        return $result->collect();
    }
}
