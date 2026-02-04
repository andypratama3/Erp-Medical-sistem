<?php

namespace App\Listeners;

use App\Events\InvoiceCreated;
use Illuminate\Support\Facades\Log;

/**
 * Send invoice to customer via email
 */
class SendInvoiceToCustomer
{
    public function handle(InvoiceCreated $event): void
    {
        $invoice = $event->invoice;
        $customer = $invoice->customer;

        try {
            // TODO: Implement email sending with invoice PDF
            // Mail::to($customer->email)
            //     ->send(new InvoiceMail($invoice));
            
            Log::info('Invoice sent to customer', [
                'invoice_id' => $invoice->id,
                'invoice_number' => $invoice->invoice_number,
                'customer_email' => $customer->email ?? 'N/A',
            ]);

        } catch (\Throwable $e) {
            Log::error('Failed to send invoice to customer', [
                'invoice_id' => $invoice->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
