<?php

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
    public static function updateOrInsert(array $attributes, string $paymentSystem): Payment
    {
        if (array_key_exists('payment', $attributes)) {
            $attributes = $attributes['payment'];
        }

        return self::updateOrCreate([
            'id' => $attributes['paymentId'],
            'system' => $paymentSystem,
        ], [
            'amount' => $attributes['amount'],
            'status' => PaymentStatus::status($attributes['state']),
            'context' => $attributes,
        ]);
    }

    /**
     * @return string|null
     */
    public function getState(): ?string
    {
        $context = $this->context;
        if (isset($context['state'])) {
            return $context['state'];
        }

        return null;
    }

    /**
     * @return int|string|null
     */
    public function getMarketPlace()
    {
        $context = $this->context;
        if (isset($context['marketPlace'])) {
            return $context['marketPlace'];
        }

        return null;
    }

    /**
     * @return string|null
     */
    public function getRecurrencyToken(): ?string
    {
        $context = $this->context;
        if (isset($context['recurrencyToken'])) {
            return $context['recurrencyToken'];
        }

        return null;
    }
}