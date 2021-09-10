<?php

namespace Fh\Purchase\Tests\Entities;

use Carbon\Carbon;
use Fh\Purchase\Casts\Payment;
use Fh\Purchase\Entities\Customer;
use Fh\Purchase\Entities\Invoice;
use Fh\Purchase\Entities\Order;
use Fh\Purchase\Entities\OrderItem;
use Fh\Purchase\Enums\OrderStatus;
use Fh\Purchase\Tests\TestCase;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;

class InvoiceTest extends TestCase
{
    use RefreshDatabase;

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
    private $payment;

    /**
     * @test
     */
    public function testCreate(): void
    {
        $this->assertInstanceOf(Invoice::class, $this->invoice);
        $this->assertDatabaseCount('purchase_invoices', 1);
        $this->assertDatabaseHas('purchase_invoices', [
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
        $this->assertArrayNotHasKey('request', $this->invoice->toArray());
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
    public function it_can_be_get_amount(): void
    {
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
    public function it_can_be_set_request_handler(): void
    {
        $this->invoice->setRequestHandler('request_handler');

        $this->assertDatabaseHas('purchase_invoices', [
            'request' => 'request_handler',
        ]);
        $this->assertIsString($this->invoice->request);
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
        $this->invoice->setPayment($this->payment);

        $this->assertNotNull($this->invoice->payment);
        $this->assertDatabaseHas('purchase_invoices', [
            'payment' => json_encode($this->payment['payment']),
            'status' => OrderStatus::status($this->payment['payment']['state']),
        ]);
        $this->assertNotEquals(OrderStatus::UNDEF, $this->invoice->status);
        $this->assertInstanceOf(Payment::class, $this->invoice->payment);
    }

    /**
     * @test
     */
    public function it_can_be_set_status(): void
    {
        $this->invoice->setStatus('end');
        $this->assertEquals(OrderStatus::END, $this->invoice->status);
    }

    /**
     * @test
     */
    public function it_can_be_get_context(): void
    {
        $orderItem = factory(OrderItem::class)->create();
        $this->order->addOrderItem($orderItem);

        $this->assertInstanceOf(OrderItem::class, $this->invoice->context());
    }

    /**
     * @test
     */
    public function it_can_be_context_collection(): void
    {
        $orderItems = factory(OrderItem::class, 3)->create();
        foreach ($orderItems as $item) {
            $this->order->addOrderItem($item);
        }

        $this->assertInstanceOf(Collection::class, $this->invoice->context());
        $this->assertInstanceOf(OrderItem::class, $this->invoice->context()->first());
    }

    /**
     * @test
     */
    public function it_can_be_closed(): void
    {
        $this->invoice->close();

        $this->assertTrue($this->invoice->isClosed());
        $this->assertDatabaseHas('purchase_invoices', [
            'status' => OrderStatus::CLOSED,
            'closed_at' => Carbon::now()->toAtomString()
        ]);
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->customer = factory(Customer::class)->create();
        $this->order = factory(Order::class)->create();
        $this->invoice = Invoice::create([
            'customer_id' => $this->customer->id,
            'order_id' => $this->order->uuid,
        ]);
        $this->payment = [
            'payment' => [
                'orderId' => $this->invoice->getOrderId(),
                'showOrderId' => date_timestamp_get(date_create()),
                'paymentId' => '1234567890',
                'account' => $this->customer->account,
                'amount' => $this->invoice->getAmount(),
                'state' => 'end',
                'marketPlace' => 000000000,
                'paymentMethod' => 'ac',
                'stateDate' => '2021-05-18T15:48:32.721+03:00',
                'email' => $this->customer->email,
                'phone' => $this->customer->phone,
                'details' => 'test',
            ]
        ];
    }
}
