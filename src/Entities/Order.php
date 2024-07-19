<?php

declare(strict_types=1);

namespace Fh\Purchase\Entities;

use Fh\Purchase\Support\HasUuid;
use Fh\Purchase\Support\HideTimestamps;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @method static self create(array $attributes = [])
 * @property Collection items
 * @property int total
 * @property float amount
 * @property string uuid
 */
class Order extends Model
{
    use HasUuid, HideTimestamps;

    public $incrementing = false;

    protected $table = 'purchase_orders';
    protected $guarded = [];
    protected $primaryKey = 'uuid';
    protected $keyType = 'string';

    protected $attributes = [
        'total' => 0,
        'amount' => 0.00,
    ];

    /**
     * @param OrderItem $orderItem
     * @return void
     */
    public function addOrderItem(OrderItem $orderItem): void
    {
        $this->items()->save($orderItem);
//        dd($orderItem);
        $this->total += $orderItem->quantity;
        $this->amount += $orderItem->price * $orderItem->quantity;
        $this->save();

        $this->refresh();
    }

    /**
     * @return HasMany
     */
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class, 'order_id', 'uuid');
    }

    /**
     * @return HasOne
     */
    public function invoice(): HasOne
    {
        return $this->hasOne(Invoice::class, 'order_id', 'uuid');
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->uuid;
    }
}
