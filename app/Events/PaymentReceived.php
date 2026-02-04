<?php

namespace App\Events;

use App\Models\FINPayment;
use App\Models\ACTInvoice;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Event fired when payment is received and confirmed
 * 
 * This event triggers:
 * - Update invoice status
 * - Send payment receipt to customer
 * - Update AR aging report
 * - Close sales order cycle
 * - Notify relevant departments
 */
class PaymentReceived
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public FINPayment $payment;
    public ACTInvoice $invoice;

    public function __construct(FINPayment $payment)
    {
        $this->payment = $payment->load([
            'invoice.customer',
            'invoice.salesDO',
        ]);
        
        $this->invoice = $this->payment->invoice;
    }
}
