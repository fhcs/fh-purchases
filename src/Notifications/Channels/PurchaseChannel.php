<?php

namespace Fh\Purchase\Notifications\Channels;

use Illuminate\Notifications\Channels\DatabaseChannel;
use Illuminate\Notifications\Notification;

class PurchaseChannel extends DatabaseChannel
{
    /**
     * @param mixed $notifiable
     * @param Notification $notification
     * @return array
     */
    protected function buildPayload($notifiable, Notification $notification): array
    {
        return [
            'id' => $notification->id,
            'type' => get_class($notification),
            'data' => $this->getData($notifiable, $notification),
            'read_at' => null,
            'handler' => $notification->getHandler(),
        ];
    }
}