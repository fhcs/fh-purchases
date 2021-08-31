<?php

declare(strict_types=1);

namespace Fh\Purchase\Entities;

use Fh\Purchase\Casts\Json;
use Fh\Purchase\Support\HideTimestamps;
use Illuminate\Database\Eloquent\Model;

/**
 * @method static self create(array $array)
 */
class OrderItem extends Model
{
    use HideTimestamps;

    protected $table = 'purchase_order_items';
    protected $guarded = [];
    protected $attributes = [
        'quantity' => 1
    ];

    protected $casts = [
        'details' => Json::class,
    ];
}
