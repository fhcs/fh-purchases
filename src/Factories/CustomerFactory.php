<?php

declare(strict_types=1);

namespace Fh\Purchase\Factories;

use Fh\Purchase\Contracts\PayableCustomer;
use Fh\Purchase\Entities\Customer;
use Illuminate\Support\Facades\Validator;

class CustomerFactory
{
    /**
     * @param mixed $customer
     * @return Customer
     */
    public function defineCustomer($customer): Customer
    {
        $attributes = $this->getAttributes($customer);

        return Customer::firstOrCreate([
            'account' => $attributes['account']
        ], [
            'phone' => $attributes['phone'],
            'email' => $attributes['email']
        ]);
    }

    /**
     * @param array|PayableCustomer $customer
     * @return array
     */
    private function getAttributes($customer): array
    {
        $attributes = [];

        if ($customer instanceof PayableCustomer) {
            $attributes = [
                'account' => $customer->getAccount(),
                'phone' => $customer->getPhone(),
                'email' => $customer->getEmail(),
            ];
        }

        if (is_array($customer)) {
            $attributes = $customer;
        }

        $this->validateAttributes($attributes);

        return $attributes;
    }

    private function validateAttributes(array $attributes): void
    {
        $validator = Validator::make($attributes, [
            'account' => ['required', 'string'],
            'phone' => ['string'],
            'email' => ['string'],
        ]);

        if ($validator->fails()) {
            throw new \InvalidArgumentException("Невозможно создать Customer. Некорректные данные");
        }
    }
}