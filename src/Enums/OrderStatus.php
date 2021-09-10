<?php

declare(strict_types=1);

namespace Fh\Purchase\Enums;

use Fh\Purchase\Support\StatusTrait;

class OrderStatus
{
    use StatusTrait;

    const NEW = 'новый';
    const SENT = 'в оплате';
    const END = 'оплачен';
    const CLOSED = 'закрыт';
    const UNDEF = 'не определен';

    const STATUS = [
        'new' => self::NEW,
        'sent' => self::SENT,
        'end' => self::END,
        'closed' => self::CLOSED,
    ];

    protected static function states(): array
    {
        return self::STATUS;
    }
}
