<?php

namespace App\Events;

use App\Models\SalesDO;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Event fired when a Sales DO is submitted from CRM to WQS
 *
 * This event triggers:
 * - Stock reservation
 * - WQS team notification
 * - Audit logging
 * - Task board update
 */
class SalesDOSubmitted
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public SalesDO $salesDo;

    public function __construct(SalesDO $salesDo)
    {
        $this->salesDo = $salesDo->load([
            'customer',
            'office',
            'items.product',
            'createdBy',
        ]);
    }
}
