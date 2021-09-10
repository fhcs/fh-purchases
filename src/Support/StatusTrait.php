<?php

declare(strict_types=1);

namespace Fh\Purchase\Support;

trait StatusTrait
{
    /**
     * @param string $state
     * @return mixed|string
     */
    public static function status(string $state): string
    {
        if (array_key_exists($state, self::states())) {
            return self::states()[$state];
        }

        return $state;
    }

    /**
     * @return array
     */
    abstract protected static function states(): array;
}
