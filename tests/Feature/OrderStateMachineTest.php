<?php

namespace Tests\Feature;

use App\Enum\OrderStatus;
use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use LogicException;
use Tests\TestCase;

/**
 * The order lifecycle is a state machine: PENDING → PRINTING → PACKING →
 * DELIVERY → COMPLETED, with cancellation available everywhere except in the
 * two terminal states. The production desk relies on `process()` refusing to
 * move a finished order any further.
 */
class OrderStateMachineTest extends TestCase
{
    use RefreshDatabase;

    public function test_order_walks_the_whole_printing_pipeline(): void
    {
        $order = Order::factory()->create();

        $this->assertSame(OrderStatus::PENDING, $order->status);

        $expected = [
            OrderStatus::PRINTING,
            OrderStatus::PACKING,
            OrderStatus::DELIVERY,
            OrderStatus::COMPLETED,
        ];

        foreach ($expected as $status) {
            $order->process();
            $order->refresh();
            $this->assertSame($status, $order->status);
        }
    }

    public function test_fresh_order_can_be_cancelled(): void
    {
        $order = Order::factory()->create();

        $order->cancel();

        $this->assertSame(OrderStatus::CANCELED, $order->fresh()->status);
    }

    public function test_order_in_printing_can_still_be_cancelled(): void
    {
        $order = Order::factory()->create();
        $order->process();

        $order->cancel();

        $this->assertSame(OrderStatus::CANCELED, $order->fresh()->status);
    }

    public function test_completed_order_cannot_be_processed_or_cancelled(): void
    {
        $order = Order::factory()->create(['status' => OrderStatus::COMPLETED]);

        $this->assertFalse($order->canBeProcessed);

        $this->expectException(LogicException::class);
        $order->process();
    }

    public function test_completed_order_cannot_be_cancelled(): void
    {
        $order = Order::factory()->create(['status' => OrderStatus::COMPLETED]);

        $this->expectException(LogicException::class);
        $order->cancel();
    }

    public function test_canceled_order_cannot_be_processed(): void
    {
        $order = Order::factory()->create(['status' => OrderStatus::CANCELED]);

        $this->assertFalse($order->canBeProcessed);

        $this->expectException(LogicException::class);
        $order->process();
    }
}
