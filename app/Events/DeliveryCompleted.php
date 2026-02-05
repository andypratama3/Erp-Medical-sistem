<?php

namespace App\Events;

use App\Models\SalesDO;
use App\Models\SCMDelivery;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Event fired when delivery is completed (POD uploaded)
 *
 * This event triggers:
 * - Create ACT task for invoicing
 * - Notify accounting team
 * - Update delivery status
 * - Send delivery confirmation to customer
 */
class DeliveryCompleted
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
