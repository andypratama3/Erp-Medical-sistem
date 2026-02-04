<?php

namespace App\Events;

use App\Models\SalesDO;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Event fired when WQS completes stock check and quality control
 * 
 * This event triggers:
 * - Create SCM task for delivery
 * - Notify SCM team
 * - Update inventory reservations
 * - Audit logging
 */
class WQSCompleted
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public SalesDO $salesDo;

    public function __construct(SalesDO $salesDo)
    {
        $this->salesDo = $salesDo->load([
            'customer',
            'office',
            'items.product',
            'branch',
        ]);
    }
}
