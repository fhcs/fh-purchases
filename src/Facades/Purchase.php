<?php

declare(strict_types=1);

namespace Fh\Purchase\Facades;

use Fh\Purchase\Contracts\PayableCustomer;
use Fh\Purchase\Contracts\PayableProduct;
use Fh\Purchase\Entities\Invoice;
use Fh\Purchase\PurchaseService;
use Illuminate\Support\Facades\Facade;

/**
 * @method static Invoice createInvoice(PayableCustomer $customer, PayableProduct $product)
 */
class Purchase extends Facade
{
    /**
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return PurchaseService::class;
    }
}
