<?php

declare(strict_types=1);

namespace Fh\Purchase\Factories;

use Fh\Purchase\Contracts\PayableProduct;
use Fh\Purchase\Entities\Order;
use Fh\Purchase\Entities\OrderItem;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;

class OrderFactory
{
    /**
     * @param mixed $product
     * @return Order
     */
    public function createOrder($product): Order
    {
        $orderItem = $this->createOrderItem($product);

        $order = Order::create();
        $order->addOrderItem($orderItem);

        return $order;
    }

    /**
     * @param $product
     * @return OrderItem
     */
    public function createOrderItem($product): OrderItem
    {
        return OrderItem::create($this->getAttributes($product));
    }

    /**
     * @param array|PayableProduct $product
     * @return array
     */
    private function getAttributes($product): array
    {
        $attributes = [];

        if ($product instanceof PayableProduct) {
            $attributes = [
                'name' => $product->getName(),
                'price' => $product->getPrice(),
                'details' => $product->toArray()
            ];
        }

        if (is_array($product)) {
            $attributes = [
                'name' => Arr::get($product, 'name', ''),
                'price' => Arr::get($product, 'price', ''),
                'details' => $product
            ];
        }

        $this->validateAttributes($attributes);

        return $attributes;
    }

    private function validateAttributes(array $attributes)
    {
        $validator = Validator::make($attributes, [
            'name' => ['required', 'string'],
            'price' => ['required', 'numeric'],
            'details' => ['required', 'array']
        ]);

        if ($validator->fails()) {
            throw new \InvalidArgumentException("Невозможно создать OrderItem. Некорректный Product");
        }
    }

    /**
     * @param array $products
     * @return Order
     */
    public function generateOrder(array $products): Order
    {
        $order = Order::create();

        foreach ($products as $product) {
            $orderItem = $this->createOrderItem($product);
            $order->addOrderItem($orderItem);
        }
        return $order;
    }
}
