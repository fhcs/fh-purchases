<?php

namespace Fh\Purchase\Entities;

use Fh\Purchase\Casts\Payment;
use Fh\Purchase\Support\HideTimestamps;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @method static Invoice create(array $attributes = [])
 * @property Payment|null payment
 * @property Order order
 * @property Customer customer
 * @property string order_id
 * @property int id
 */
class Invoice extends Model
{
    use HideTimestamps;

    protected $table = 'purchase_invoices';
    protected $guarded = [];

    protected $casts = [
        'payment' => Payment::class,
    ];

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
}
