<?php

namespace App\Events;

use App\Models\ACTInvoice;
use App\Models\SalesDO;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Event fired when invoice is approved and ready for collection
 *
 * This event triggers:
 * - Create FIN collection task
 * - Notify finance team
 * - Send final invoice to customer
 * - Start payment collection process
 */
class InvoiceApproved
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public ACTInvoice $invoice;
    public SalesDO $salesDo;

    /**
     * Create a new event instance.
     */
    public function __construct(ACTInvoice $invoice, SalesDO $salesDo)
    {
        $this->invoice = $invoice;
        $this->salesDo = $salesDo;
    }
}
