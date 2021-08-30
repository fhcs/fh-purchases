<?php

declare(strict_types=1);

namespace Fh\Purchase\Facades;

use Fh\Purchase\Entities\Customer;
use Fh\Purchase\Factories\CustomerFactory;
use Illuminate\Support\Facades\Facade;

/**
 * @method static Customer defineCustomer($customer)
 */
class CustomerFactoryFacade extends Facade
{
    /**
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return CustomerFactory::class;
    }
}