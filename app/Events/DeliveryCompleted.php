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

    public SCMDelivery $delivery;
    public SalesDO $salesDo;

    public function __construct(SCMDelivery $delivery)
    {
        $this->delivery = $delivery->load([
            'salesDO.customer',
            'salesDO.items.product',
            'driver',
        ]);
        
        $this->salesDo = $this->delivery->salesDO;
    }
}
