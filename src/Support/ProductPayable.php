<?php

declare(strict_types=1);

namespace Fh\Purchase\Support;

use Fh\Purchase\Contracts\PayableProduct;

trait ProductPayable
{
    /**
     * @param string $name
     * @param float $price
     * @param array $attributes
     * @return PayableProduct
     */
    protected function payableProduct(string $name, float $price, array $attributes = []): PayableProduct
    {
        return new class($name, $price, $attributes) implements PayableProduct {

            private $price;
            private $name;
            private $attributes;

            /**
             * @param string $name
             * @param float $price
             * @param array $attributes
             */
            public function __construct(string $name, float $price, array $attributes = [])
            {
                $this->name = $name;
                $this->price = $price;

                $this->attributes = $attributes;
                foreach ($this->attributes as $field => $value) {
                    $this->{$field} = $value;
                }
            }


            /**
             * @return array
             */
            public function toArray(): array
            {
                $result = [
                    'name' => $this->getName(),
                    'price' => $this->getPrice(),
                ];

                return array_merge($result, $this->attributes);
            }

            public function getName(): string
            {
                return $this->name;
            }

            public function getPrice(): float
            {
                return $this->price;
            }
        };
    }
}
