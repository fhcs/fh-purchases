<?php

namespace Fh\Purchase\Entities;

use Fh\Purchase\Enums\OrderStatus;
use Fh\Purchase\Enums\PaymentStatus;
use Fh\Purchase\Notifications\PurchaseNotifiable;
use Fh\Purchase\Support\HideTimestamps;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Illuminate\Support\LazyCollection;

/**
 * @method static Invoice create(array $attributes = [])
 * @method static whereOrderId(string $orderId)
 * @method static LazyCollection cursor()
 * @property Payment|null payment
 * @property Order order
 * @property Customer customer
 * @property string order_id
 * @property int id
 * @property string $request
 * @property string $status
 * @property string $target
 */
class Invoice extends Model
{
    use HideTimestamps, PurchaseNotifiable;

    protected $table = 'purchase_invoices';
    protected $guarded = [];
    protected $attributes = [
        'status' => OrderStatus::NEW
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
    public function getTarget(): string
    {
        return $this->target;
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
     * @deprecated
     * @see getOrderAmount
     */
    public function getAmount(): float
    {
        return $this->order->amount;
    }

    /**
     * @return float
     */
    public function getOrderAmount(): float
    {
        return $this->order->amount;
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
     * @param Payment $payment
     * @return void
     */
    public function setPayment(Payment $payment): void
    {
        $this->payment()
            ->associate($payment)
            ->save();

        $this->updateStatusByPayment();
    }

    /**
     * @return BelongsTo
     */
    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }

    public function updateStatusByPayment()
    {
        if (!is_null($payment = $this->payment)) {
            $this->setStatusByPayment($payment);
        }
    }

    /**
     * @param Payment $payment
     * @return void
     */
    public function setStatusByPayment(Payment $payment): void
    {
        if (PaymentStatus::isFinalState($payment->status)) {
            $status = OrderStatus::TREATED;
            if ($payment->status === PaymentStatus::END) {
                $status = OrderStatus::PAID;
            }
        } else {
            $status = OrderStatus::PROCESSING;
        }

        $this->setStatus($status);
    }

    /**
     * @param string $state
     * @return void
     */
    public function setStatus(string $state): void
    {
        self::update(['status' => OrderStatus::status($state)]);
    }

    /**
     * @return int|null
     */
    public function getPaymentId(): ?int
    {
        return is_null($this->payment) ? null : $this->payment->id;
    }

    /**
     * @return float|null
     */
    public function getPaymentAmount(): ?float
    {
        return is_null($this->payment) ? null : $this->payment->amount;
    }

    /**
     * @return string|null
     */
    public function getPaymentStatus(): ?string
    {
        return is_null($this->payment) ? null : $this->payment->status;
    }

    /**
     * @return string|null
     * @deprecated
     * @see getPaymentStatus
     */
    public function getPaymentState(): ?string
    {
        if (!is_null($payment = $this->payment)) {
            return $payment->getState();
        }

        return null;
    }

    /**
     * @return int|null
     */
    public function getPaymentMarketPlace(): ?int
    {
        if (!is_null($payment = $this->payment)) {
            return $payment->getMarketPlace();
        }

        return null;
    }

    /**
     * @return string|null
     */
    public function getPaymentRecurrencyToken(): ?string
    {
        if (!is_null($payment = $this->payment)) {
            return $payment->getRecurrencyToken();
        }

        return null;
    }

    /**
     * @return void
     */
    public function close()
    {
        self::update([
            'status' => OrderStatus::CLOSED,
            'closed_at' => Carbon::now(),
        ]);
    }

    /**
     * @return Collection
     */
    public function orderItems(): Collection
    {
        return $this->order->items;
    }

    /**
     * @return bool
     */
    public function isClosed(): bool
    {
        return $this->status === OrderStatus::CLOSED;
    }

    /**
     * @return bool
     */
    public function isPaid(): bool
    {
        return $this->status === OrderStatus::PAID || $this->payment && $this->payment->status === PaymentStatus::END;
    }
}
