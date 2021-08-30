<?php

namespace Fh\Purchase\Tests;

use Fh\Purchase\PurchaseServiceProvider;
use Orchestra\Testbench\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    protected function getPackageProviders($app)
    {
        return [
            PurchaseServiceProvider::class,
        ];
    }
}
