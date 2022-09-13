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

    /**
     * @test
     */
    public function it_can_be_payable_product_with_attributes(): void
    {
        $attributes = [
            'type' => 'test_product',
            'price' => '100',
            'name' => 'Test product'
        ];

        $product = $this->payableProduct('Product test', 100.00, $attributes);

        $this->assertInstanceOf(PayableProduct::class, $product);
        $this->assertEquals([
            'name' => 'Product test',
            'price' => 100.00,
            'type' => 'test_product'
        ], $product->toArray());
        $this->assertEquals(100.00, $product->getPrice());
    }
}
