<?php

declare(strict_types=1);

namespace Fh\Purchase\Entities;

use Fh\Purchase\Enums\PaymentStatus;
use Fh\Purchase\Support\HideTimestamps;
use Illuminate\Database\Eloquent\Model;

/**
 * @method static Payment updateOrCreate(array $array, array $array1)
 * @property string $status
 * @property float $amount
 * @property int $id
 * @property array $context
 */
class Payment extends Model
{
    use HideTimestamps;

    public $incrementing = false;

    protected $table = 'purchase_payments';
    protected $guarded = [];

    protected $casts = [
        'context' => 'array' //PaymentContext::class,
    ];

    /**
     * @param array $attributes
     * @param string $paymentSystem
     * @return Payment
     */
    public static function updateOrInsert(array $attributes, string $paymentSystem = ''): Payment
    {
        return self::updateOrCreate([
            'id' => $attributes['paymentId'],
            'system' => $paymentSystem ?? config('payment.system') ?? '',
        ], [
            'amount' => $attributes['amount'],
            'status' => PaymentStatus::status($attributes['state']),
            'context' => $attributes,
        ]);
    }
}