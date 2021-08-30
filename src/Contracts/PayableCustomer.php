<?php

declare(strict_types=1);

namespace Fh\Purchase\Contracts;

interface PayableCustomer
{
    /**
     * @return string
     */
    public function getAccount(): string;

    /**
     * @return string
     */
    public function getEmail(): string;

    /**
     * @return string
     */
    public function getPhone(): string;

}
