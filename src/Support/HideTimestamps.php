<?php

declare(strict_types=1);

namespace Fh\Purchase\Support;

trait HideTimestamps
{
    /**
     * @return string[]
     */
    public function getHidden(): array
    {
        return [
            'created_at',
            'updated_at'
        ];
    }
}
