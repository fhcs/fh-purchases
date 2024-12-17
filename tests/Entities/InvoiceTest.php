<?php

namespace Fh\Purchase\Tests\Entities;

use Carbon\Carbon;
use Fh\Purchase\Entities\Customer;
use Fh\Purchase\Entities\Invoice;
use Fh\Purchase\Entities\Order;
use Fh\Purchase\Entities\OrderItem;
use Fh\Purchase\Entities\Payment;
use Fh\Purchase\Enums\OrderStatus;
use Fh\Purchase\Enums\PaymentStatus;
use Fh\Purchase\Tests\TestCase;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

class InvoiceTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    const TARGET_PAYMENT = 'Тестовая оплата';

    /**
     * @var Invoice
     */
    private $invoice;
    /**
     * @var Customer
     */
    private $customer;

    /**
     * @var Order
     */
    private $order;

    /**
     * @var array
     */
    private $paymentData;

    /**
     * @var Payment
     */
    private $payment;

    /**
     * @test
     */
    public function testCreate(): void
    {
        $this->assertInstanceOf(Invoice::class, $this->invoice);
        $this->assertDatabaseCount('purchase_invoices', 1);
        $this->assertDatabaseHas('purchase_invoices', [
            'target' => self::TARGET_PAYMENT,
            'customer_id' => $this->customer->id,
            'order_id' => $this->order->uuid,
            'status' => OrderStatus::NEW
        ]);

        $this->assertNull($this->invoice->payment);
        $this->assertNull($this->invoice->request);

        $this->assertEquals(1, Invoice::has('order')->count());
        $this->assertEquals(1, Invoice::has('customer')->count());

        $this->assertArrayNotHasKey('created_at', $this->invoice->toArray());
        $this->assertArrayNotHasKey('updated_at', $this->invoice->toArray());
    }

    /**
     * @test
     */
    public function it_can_be_get_order_id(): void
    {
        $this->assertEquals($this->invoice->getOrderId(), $this->order->getId());
    }

    /**
     * @test
     */
    public function it_can_be_get_order_amount(): void
    {
        $this->assertEquals($this->invoice->getOrderAmount(), $this->order->amount);
        $this->assertEquals($this->invoice->getAmount(), $this->order->amount);
    }

    public function testCustomer()
    {
        $this->assertDatabaseHas('purchase_invoices', [
            'customer_id' => $this->customer->id,
        ]);

        $this->assertEquals(1, Invoice::has('customer')->count());

        $this->assertInstanceOf(Customer::class, $this->invoice->customer);
        $this->assertInstanceOf(Collection::class, $this->customer->invoices);
        $this->assertInstanceOf(Invoice::class, $this->customer->invoices()->first());
    }

    public function testOrder()
    {
        $this->assertDatabaseHas('purchase_invoices', [
            'order_id' => $this->order->uuid,
        ]);

        $this->assertEquals(1, Invoice::has('order')->count());

        $this->assertInstanceOf(Order::class, $this->invoice->order);
        $this->assertInstanceOf(Invoice::class, $this->order->invoice);
    }

    /**
     * @test
     */
    public function it_can_be_find_by_order_id(): void
    {
        $invoice = Invoice::findByOrderId($this->order->getId());
        $this->assertInstanceOf(Invoice::class, $invoice);
    }

    /**
     * @test
     */
    public function is_customer_account(): void
    {
        $this->assertTrue($this->invoice->isCustomerAccount($this->customer->account));
        $this->assertFalse($this->invoice->isCustomerAccount('invalid'));
    }

    /**
     * @test
     */
    public function it_can_be_set_payment(): void
    {
        $this->invoice->setPayment(Payment::updateOrInsert($this->paymentData, self::PAYMENT_SYSTEM));

        $this->assertNotNull($this->invoice->payment);
        $this->assertDatabaseHas('purchase_invoices', [
            'target' => self::TARGET_PAYMENT,
            'status' => $this->invoice->status,
            'payment_id' => $this->paymentData['payment']['paymentId'],
        ]);
        $this->assertNotEquals(OrderStatus::UNDEF, $this->invoice->status);
        $this->assertInstanceOf(Payment::class, $this->invoice->payment);
    }

    /**
     * @test
     */
    public function it_can_be_update_status_by_payment(): void
    {
        $this->invoice->setPayment(Payment::updateOrInsert($this->paymentData, self::PAYMENT_SYSTEM));
        $this->assertEquals(OrderStatus::NEW, $this->invoice->status);

        $this->invoice->updateStatusByPayment();
        $this->assertEquals(OrderStatus::PAID, $this->invoice->status);
    }

    /**
     * @test
     */
    public function it_not_can_be_set_status_if_closed(): void
    {
        $invoice = Invoice::create([
            'status' => OrderStatus::CLOSED,
            'target' => self::TARGET_PAYMENT,
            'customer_id' => $this->customer->id,
            'order_id' => $this->order->uuid,
            'payment_id' => $this->payment->id
        ]);
        $invoice->setPayment(Payment::updateOrInsert($this->paymentData, self::PAYMENT_SYSTEM));

        $this->assertEquals(OrderStatus::CLOSED, $invoice->status);
    }

    /**
     * @test
     */
    public function it_can_be_get_payment_id(): void
    {
        $this->assertNull($this->invoice->getPaymentId());

        $this->invoice->setPayment(Payment::updateOrInsert($this->paymentData, self::PAYMENT_SYSTEM));
        $this->assertEquals($this->paymentData['payment']['paymentId'], $this->invoice->getPaymentId());
    }

    /**
     * @test
     */
    public function it_can_be_get_payment_amount(): void
    {
        $this->assertNull($this->invoice->getPaymentAmount());

        $this->invoice->setPayment(Payment::updateOrInsert($this->paymentData, self::PAYMENT_SYSTEM));
        $this->assertEquals($this->paymentData['payment']['amount'], $this->invoice->getPaymentAmount());
    }

    /**
     * @test
     */
    public function it_can_be_get_payment_status(): void
    {
        $this->assertNull($this->invoice->getPaymentStatus());

        $this->invoice->setPayment(Payment::updateOrInsert($this->paymentData, self::PAYMENT_SYSTEM));
        $this->assertEquals(
            PaymentStatus::status($this->paymentData['payment']['state']),
            $this->invoice->getPaymentStatus()
        );
    }

    /**
     * @test
     */
    public function it_can_be_get_payment_state(): void
    {
        $this->assertNull($this->invoice->getPaymentState());

        $this->invoice->setPayment(Payment::updateOrInsert($this->paymentData, self::PAYMENT_SYSTEM));
        $this->assertEquals(
            $this->paymentData['payment']['state'],
            $this->invoice->getPaymentState()
        );
    }

    /**
     * @test
     */
    public function it_can_be_get_payment_market_place(): void
    {
        $this->assertNull($this->invoice->getPaymentMarketPlace());

        $this->invoice->setPayment(Payment::updateOrInsert($this->paymentData, self::PAYMENT_SYSTEM));
        $this->assertEquals(
            $this->paymentData['payment']['marketPlace'],
            $this->invoice->getPaymentMarketPlace()
        );
    }

    /**
     * @test
     */
    public function it_can_be_get_payment_recurrency_token(): void
    {
        $this->assertNull($this->invoice->getPaymentRecurrencyToken());

        $this->invoice->setPayment(Payment::updateOrInsert($this->paymentData, self::PAYMENT_SYSTEM));
        $this->assertEquals(
            $this->paymentData['payment']['recurrencyToken'],
            $this->invoice->getPaymentRecurrencyToken()
        );
    }

    /**
     * @test
     */
    public function it_can_be_set_invoice_status(): void
    {
        $this->invoice->setStatus('end');
        $this->assertEquals(OrderStatus::END, $this->invoice->status);
        $this->assertEquals(OrderStatus::PAID, $this->invoice->status);
    }

    /**
     * @test
     */
    public function it_can_be_order_items_collection(): void
    {
        $orderItems = factory(OrderItem::class, 3)->create();
        foreach ($orderItems as $item) {
            $this->order->addOrderItem($item);
        }

        $this->assertInstanceOf(Collection::class, $this->invoice->orderItems());
        $this->assertInstanceOf(OrderItem::class, $this->invoice->orderItems()->first());
    }

    /**
     * @test
     */
    public function it_can_be_closed(): void
    {
        $this->invoice->close();

        $this->assertTrue($this->invoice->isClosed());
        $this->assertDatabaseHas('purchase_invoices', [
            'target' => self::TARGET_PAYMENT,
            'status' => OrderStatus::CLOSED,
            'closed_at' => Carbon::now()
        ]);
    }

    /**
     * @test
     */
    public function it_can_be_is_paid(): void
    {
        $this->assertFalse($this->invoice->isPaid());

        $this->invoice->setStatus(OrderStatus::PAID);
        $this->assertTrue($this->invoice->isPaid());
    }

    /**
     * @test
     */
    public function it_can_be_get_target(): void
    {
        $this->assertEquals(self::TARGET_PAYMENT, $this->invoice->getTarget());
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->customer = factory(Customer::class)->create();
        $this->order = factory(Order::class)->create();

        $orderItems = factory(OrderItem::class, 3)->create();
        foreach ($orderItems as $item) {
            $this->order->addOrderItem($item);
        }

        $this->invoice = Invoice::create([
            'target' => self::TARGET_PAYMENT,
            'customer_id' => $this->customer->id,
            'order_id' => $this->order->uuid,
        ]);

        $this->paymentData = [
            'payment' => [
                'orderId' => $this->invoice->getOrderId(),
                'showOrderId' => date_timestamp_get(date_create()),
                'paymentId' => $this->faker->randomNumber(9),
                'account' => $this->customer->account,
                'amount' => $this->invoice->getOrderAmount(),
                'state' => 'end',
                'marketPlace' => $this->faker->randomNumber(7),
                'paymentMethod' => 'ac',
                'stateDate' => $this->faker->dateTime()->format('Y-m-d\TH:i:sP'),
                'recurrencyToken' => $this->faker->uuid(),
                'email' => $this->customer->email,
                'phone' => $this->customer->phone,
                'details' => 'test',
            ]
        ];

        $this->payment = factory(Payment::class)->create([
            'system' => self::PAYMENT_SYSTEM,
            'status' => PaymentStatus::END,
            'context' => $this->paymentData['payment']
        ]);
    }
}
