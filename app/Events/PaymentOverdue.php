<?php

namespace App\Events;

use App\Models\ACTInvoice;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Event fired when payment becomes overdue
 * 
 * This event triggers:
 * - Send overdue notice to customer
 * - Escalate to collection team
 * - Update credit status
 * - Generate aging report
 * - Notify management
 */
class PaymentOverdue
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public ACTInvoice $invoice;
    public int $daysOverdue;

    public function __construct(ACTInvoice $invoice)
    {
        $this->invoice = $invoice->load([
            'customer',
            'salesDO',
            'paymentTerm',
        ]);
        
        $this->daysOverdue = now()->diffInDays($invoice->due_date, false);
    }
}
