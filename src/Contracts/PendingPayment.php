<?php

namespace Fh\Purchase\Contracts;

use Fh\Purchase\Entities\Invoice;

interface PendingPayment
{
    public function payUrl(): string;

    public function invoice(): Invoice;
}