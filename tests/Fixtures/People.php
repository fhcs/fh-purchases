<?php

namespace Fh\Purchase\Tests\Fixtures;

use Fh\Purchase\Contracts\PayableCustomer;

class People implements PayableCustomer
{

    private $phone;
    private $email;

    /**
     * @param string $phone
     * @param string $email
     */
    public function __construct(string $phone = '+7(123)456-78-90', string $email = 'test@test.tt')
    {
        $this->phone = $phone;
        $this->email = $email;
    }


    public function getAccount(): string
    {
        return phone_digits($this->getPhone());
    }

    public function getPhone(): string
    {
        return $this->phone;
    }

    public function getEmail(): string
    {
        return $this->email;
    }
}