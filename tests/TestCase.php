<?php

namespace Fh\Purchase\Tests;

use Fh\Purchase\Facades\CustomerFactoryFacade;
use Fh\Purchase\Facades\InvoiceFactoryFacade;
use Fh\Purchase\Facades\OrderFactoryFacade;
use Fh\Purchase\Facades\PurchaseFacade;
use Fh\Purchase\PurchaseServiceProvider;
use Orchestra\Testbench\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    public static function assertIsUrl($actual, string $message = '')
    {
        static::assertTrue(!!filter_var($actual, FILTER_VALIDATE_URL), $message);
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->withFactories(__DIR__ . '/../database/factories');
    }

    protected function getPackageProviders($app): array
    {
        return [
            PurchaseServiceProvider::class,
        ];
    }

    protected function getPackageAliases($app): array
    {
        return [
            'OrderFactoryFacade' => OrderFactoryFacade::class,
            'CustomerFactoryFacade' => CustomerFactoryFacade::class,
            'InvoiceFactoryFacade' => InvoiceFactoryFacade::class,
            'PurchaseFacade' => PurchaseFacade::class
        ];
    }
}
