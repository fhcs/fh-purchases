<?php

namespace Fh\Purchase\Notifications;

use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Notifications\Notifiable;

trait PurchaseNotifiable
{
    use Notifiable;

    /**
     * @param string|null $handler
     * @return void
     */
    public function notifyPayment(string $handler = null)
    {
        $this->notify(new InvoicePayment($handler));
    }

    public function getHandler()
    {
        return $this->notification() ? $this->notification()->handler : null;
    }

    public function notification()
    {
        return $this->notifications()->first();
    }

    /**
     * @return MorphMany
     */
    public function notifications(): MorphMany
    {
        return $this->morphMany(PurchaseNotification::class, 'notifiable')->orderBy('created_at', 'desc');
    }
}