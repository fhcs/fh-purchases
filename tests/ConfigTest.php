<?php

namespace Fh\Purchase\Tests;

class ConfigTest extends TestCase
{
    /**
     * @test
     */
    public function it_can_be_get_config_purchase(): void
    {
        $config = config('purchases');
        $this->assertIsArray($config);
    }
}
