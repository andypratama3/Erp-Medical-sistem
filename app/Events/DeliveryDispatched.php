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

    public SCMDelivery $delivery;
    public SalesDO $salesDo;

    public function __construct(SCMDelivery $delivery)
    {
        $this->delivery = $delivery->load([
            'salesDO.customer',
            'driver',
            'vehicle',
        ]);
        
        $this->salesDo = $this->delivery->salesDO;
    }
}
