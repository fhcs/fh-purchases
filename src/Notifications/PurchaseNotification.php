<?php

namespace Fh\Purchase\Notifications;

use Illuminate\Notifications\DatabaseNotification;

class PurchaseNotification extends DatabaseNotification
{
    protected $table = 'purchase_notifications';
}