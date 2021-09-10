<?php

namespace Fh\Purchase\Entities;

use Fh\Purchase\Casts\Payment;
use Fh\Purchase\Enums\OrderStatus;
use Fh\Purchase\Support\HideTimestamps;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @method static Invoice create(array $attributes = [])
 * @method static whereOrderId(string $orderId)
 * @property Payment|null payment
 * @property Order order
 * @property Customer customer
 * @property string order_id
 * @property int id
 * @property string $request
 * @property string $status
 */
class Invoice extends Model
{
    use HideTimestamps;

    protected $table = 'purchase_invoices';
    protected $guarded = [];
    protected $attributes = [
        'status' => OrderStatus::NEW
    ];
    protected $casts = [
        'payment' => Payment::class,
    ];
    protected $hidden = [
        'request'
    ];

    /**
     * @param string $orderId
     * @return Invoice|null
     */
    public static function findByOrderId(string $orderId): ?Invoice
    {
        return self::whereOrderId($orderId)->first();
    }

    /**
     * @return BelongsTo
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * @return BelongsTo
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'order_id', 'uuid');
    }

    /**
     * @return string
     */
    public function getOrderId(): string
    {
        return $this->order_id;
    }

    /**
     * @return float
     */
    public function getAmount(): float
    {
        return $this->order->amount;
    }

    /**
     * @param string $requestHandler
     */
    public function setRequestHandler(string $requestHandler)
    {
        self::update(['request' => $requestHandler]);
    }

    /**
     * @param string $customerAccount
     * @return bool
     */
    public function isCustomerAccount(string $customerAccount): bool
    {
        if (!$this->customer || $this->customer->account != $customerAccount) {
            return false;
        }

        return true;
    }

    /**
     * @param array $parameters
     */
    public function setPayment(array $parameters)
    {
        if (array_key_exists('payment', $parameters)) {
            $parameters = $parameters['payment'];
        }

        $paymentStatus = $parameters['state']
            ? OrderStatus::status($parameters['state'])
            : OrderStatus::UNDEF;

        self::update([
            'payment' => $parameters,
            'status' => $paymentStatus,
            'closed_at' => OrderStatus::isFinalState($paymentStatus) ? Carbon::now()->toAtomString() : null
        ]);
    }

    /**
     * @param string $state
     * @return void
     */
    public function setStatus(string $state)
    {
        self::update(['status' => OrderStatus::status($state)]);
    }

    /**
     * @return Collection|OrderItem
     */
    public function context()
    {
        $context = $this->order->items;

        return ($context->count() === 1) ? $context->first() : $context;
    }
}
