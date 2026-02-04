<?php

namespace App\Events;

use App\Models\ACTInvoice;
use App\Models\SalesDO;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Event fired when invoice is created by accounting
 * 
 * This event triggers:
 * - Send invoice to customer
 * - Create payment reminder schedule
 * - Notify finance team
 * - Update AR aging report
 */
class InvoiceCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public ACTInvoice $invoice;
    public SalesDO $salesDo;

    public function __construct(ACTInvoice $invoice)
    {
        $this->invoice = $invoice->load([
            'customer',
            'salesDO.items',
            'branch',
        ]);
        
        $this->salesDo = $this->invoice->salesDO;
    }
}
