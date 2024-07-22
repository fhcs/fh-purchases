<?php

declare(strict_types=1);

namespace Fh\Purchase\Facades;

use Fh\Purchase\Entities\Order;
use Fh\Purchase\Factories\OrderFactory;
use Illuminate\Support\Facades\Facade;

/**
 * @method static Order createOrder($product)
 * @method static Order generateOrder(array $products)
 */
class OrderFactoryFacade extends Facade
{
    /**
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return OrderFactory::class;
    }
}
