<?php

namespace App\Events;

use App\Models\SalesDO;
use App\Models\WQSStockCheck;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Event fired when stock check fails (insufficient stock)
 * 
 * This event triggers:
 * - Hold sales order
 * - Notify purchasing/procurement
 * - Create purchase order (if configured)
 * - Send customer notification about delay
 * - Update CRM status
 */
class StockCheckFailed
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public WQSStockCheck $stockCheck;
    public SalesDO $salesDo;
    public array $problematicItems;

    public function __construct(WQSStockCheck $stockCheck, array $problematicItems = [])
    {
        $this->stockCheck = $stockCheck->load([
            'salesDO.customer',
            'items.product',
        ]);
        
        $this->salesDo = $this->stockCheck->salesDO;
        $this->problematicItems = $problematicItems;
    }
}
