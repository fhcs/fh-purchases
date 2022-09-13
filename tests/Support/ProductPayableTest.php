<?php

namespace Fh\Purchase\Tests\Support;

use Fh\Purchase\Contracts\PayableProduct;
use Fh\Purchase\Support\ProductPayable;
use Fh\Purchase\Tests\TestCase;

class ProductPayableTest extends TestCase
{
    use ProductPayable;

    /**
     * @test
     */
    public function it_can_create_payable_product(): void
    {
        $product = $this->payableProduct('Test product', 100.00, ['type' => 'test_product']);

        $this->assertInstanceOf(PayableProduct::class, $product);
        $this->assertEquals([
            'name' => 'Test product',
            'price' => 100.00,
            'type' => 'test_product'
        ], $product->toArray());
    }
}
