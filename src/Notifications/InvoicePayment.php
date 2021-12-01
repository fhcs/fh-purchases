<?php

namespace Fh\Purchase\Notifications;

use Fh\Purchase\Entities\Invoice;
use Fh\Purchase\Notifications\Channels\PurchaseChannel;
use Illuminate\Notifications\Notification;

class InvoicePayment extends Notification
{
    /**
     * @var string
     */
    private $handler;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(string $handler = null)
    {
        $this->handler = $handler;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param Invoice $notifiable
     * @return array
     */
    public function via(Invoice $notifiable): array
    {
        return [PurchaseChannel::class];
    }

    /**
     * Get the array representation of the notification.
     *
     * @param Invoice $notifiable
     * @return array
     */
    public function toArray(Invoice $notifiable): array
    {
        return [
            'order_id' => $notifiable->getOrderId(),
            'customer' => $notifiable->customer->email,
        ];
    }

    /**
     * @return string
     */
    public function getHandler(): ?string
    {
        return $this->handler;
    }

}