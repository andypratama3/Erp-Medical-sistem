<?php

namespace App\Events;

use App\Models\SalesDO;
use App\Models\SCMDelivery;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Event fired when delivery is dispatched (on the way)
 *
 * This event triggers:
 * - Start delivery tracking
 * - Notify customer
 * - Send SMS/Email with tracking link
 * - Update estimated delivery time
 */
class DeliveryDispatched
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public SalesDO $salesDo;
    public SCMDelivery $delivery;

    /**
     * Create a new event instance.
     */
    public function __construct(SalesDO $salesDo, SCMDelivery $delivery)
    {
        $this->salesDo = $salesDo;
        $this->delivery = $delivery;
    }
}
