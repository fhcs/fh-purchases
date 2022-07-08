<?php

namespace Fh\Purchase\Contracts;

use Fh\Purchase\Entities\Invoice;

interface PendingPayment
{
    /**
     * @return string
     */
    public function getPayUrl(): string;

    /**
     * @return PayableProduct
     */
    public function createProduct(): PayableProduct;

    /**
     * @return Invoice
     */
    public function createInvoice(): Invoice;

    /**
     * @return mixed
     */
    public function createPaymentQuery();

    /**
     * @return self
     */
    public function create(): self;
}