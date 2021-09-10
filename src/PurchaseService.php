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
     * @return Invoice
     */
    public function createInvoice(PayableCustomer $customer, PayableProduct $product): Invoice
    {
        return tap(InvoiceFactory::createInvoice($customer, $product), function ($invoice) {
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

//    /**
//     * @param string $typeRequest
//     * @param Invoice $invoice
//     * @param string $paymentSystem
//     * @return QueryBuilder
//     */
//    public function paymentQuery(string $typeRequest, Invoice $invoice, string $paymentSystem = ''): QueryBuilder
//    {
//        return $this->getPaymentQuery($paymentSystem, $invoice);
//    }
//
//    /**
//     * @param string $paymentSystem
//     * @param Invoice $invoice
//     * @return QueryBuilder
//     */
//    private function getPaymentQuery(string $paymentSystem, Invoice $invoice): QueryBuilder
//    {
//        return PaymentQuery::paymentSystem($paymentSystem)->create(function (QueryBuilder $query) use ($invoice) {
//            $query->amount($invoice->getAmount());
//            $query->orderId($invoice->getOrderId());
//            $query->customer($invoice->customer->toArray());
//        });
//    }
}
