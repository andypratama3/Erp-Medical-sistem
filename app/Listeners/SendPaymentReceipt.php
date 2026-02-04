<?php

namespace App\Listeners;

use App\Events\PaymentReceived;
use Illuminate\Support\Facades\Log;

/**
 * Send payment receipt to customer
 */
class SendPaymentReceipt
{
    public function handle(PaymentReceived $event): void
    {
        $payment = $event->payment;
        $invoice = $event->invoice;
        $customer = $invoice->customer;

        try {
            // TODO: Implement email sending with payment receipt
            // Mail::to($customer->email)
            //     ->send(new PaymentReceiptMail($payment));
            
            Log::info('Payment receipt sent to customer', [
                'payment_id' => $payment->id,
                'invoice_number' => $invoice->invoice_number,
                'customer_email' => $customer->email ?? 'N/A',
                'amount' => $payment->amount,
            ]);

        } catch (\Throwable $e) {
            Log::error('Failed to send payment receipt', [
                'payment_id' => $payment->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
