<?php

declare(strict_types=1);

namespace Fh\Purchase\Events;

use Fh\Purchase\Entities\Invoice;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class InvoiceCreated
{
    use Dispatchable, SerializesModels;

    /**
     * @var Invoice
     */
    public $invoice;

    public function __construct(Invoice $invoice)
    {
        $this->invoice = $invoice;
    }
}
