<?php

declare(strict_types=1);

namespace Fh\Purchase\Enums;

use Fh\Purchase\Support\StatusTrait;

class OrderStatus
{
    use StatusTrait;

    /**
     * Создан новый заказ,
     * еще не отправлен на обработку в платежную систему,
     * платежная система о заказе ничего не знает
     */
    const NEW = 'новый';

    /**
     * Заказ отправлен в платежную систему на обработку.
     * Ответ от платежной системы о состоянии платежа не получен.
     */
    const SENT = 'отправлен в ПС';

    /**
     * Заказ принят к оплате и обрабатывается платежной системой.
     * По запросу от платежной системы получает ответ со
     * статусом промежуточного состояния платежа.
     */
    const PROCESSING = 'обрабатывается ПС';

    /**
     * Заказ обработан ПС.
     * По запросу от платежной системы получает ответ со
     * статусом конечного состояния платежа
     * (оплачен, возвращен, просрочен, отменен, ошибка, отвергнут).
     */
    const TREATED = 'обработан ПС';

    /**
     * Заказ в ПС не найден
     * По запросу к платежной системе получает ответ
     * со статусом например (ПСКБ): STATUS_FAILURE, errorCode == UNKNOWN_PAYMENT
     */
    const NOT_FOUND = 'не найден в ПС';

    /**
     * Ошибка запроса к ПС
     * По запросу к платежной системе получает ответ
     * со статусом, например (ПСКБ): STATUS_FAILURE, errorCode != UNKNOWN_PAYMENT
     */
    const ERROR = 'error';

    /**
     * Заказ оплачен.
     * Заказ оплачен в ПС, по запросу к платежной системе получен ответ
     * со статусом 'end'
     */
    const PAID = 'оплачен';

    /**
     * @deprecated
     * @see PAID
     */
    const END = 'оплачен';

    /**
     * Заказ закрыт.
     * Заказ оплачен в ПС, направлен в центральную базу
     * и успешно обработан центральной базой
     */
    const CLOSED = 'закрыт';

    /**
     * Заказ не оплачен.
     * Заказ создан в ПС со статусом `SENT` (в оплате).
     * Плательщик перенаправлен в магазин по URL в случае неуспешной оплаты.
     */
    const FAILED_PAY = 'не удачная оплата';

    const UNDEF = 'не определен';

    const STATUS = [
        'new' => self::NEW,
        'sent' => self::SENT,
        'processing' => self::PROCESSING,
        'treated' => self::TREATED,
        'not_found' => self::NOT_FOUND,
        'paid' => self::PAID,
        'end' => self::END,
        'closed' => self::CLOSED,
        'error' => self::ERROR,
        'undefined' => self::UNDEF,
        'failed_pay' => self::FAILED_PAY,
    ];
}
