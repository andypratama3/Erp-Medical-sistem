<?php

namespace App\Listeners;

use App\Events\PaymentReceived;
use App\Services\AuditLogService;
use Illuminate\Support\Facades\Log;

/**
 * Close the complete sales order cycle when payment is received
 */
class CloseSalesOrderCycle
{
    protected AuditLogService $auditLog;

    public function __construct(AuditLogService $auditLog)
    {
        $this->auditLog = $auditLog;
    }

    public function handle(PaymentReceived $event): void
    {
        $payment = $event->payment;
        $invoice = $event->invoice;
        $salesDo = $invoice->salesDO;

        try {
            // Update Sales DO to completed status
            $salesDo->update([
                'status' => 'fin_paid',
                'completed_at' => now(),
            ]);

            // Complete all related task boards
            $salesDo->taskBoards()->update([
                'task_status' => 'completed',
                'completed_at' => now(),
            ]);

            // Log cycle completion
            $this->auditLog->log('SALES_CYCLE_COMPLETED', 'FIN', [
                'do_code' => $salesDo->do_code,
                'invoice_number' => $invoice->invoice_number,
                'payment_id' => $payment->id,
                'total_amount' => $payment->amount,
                'customer' => $invoice->customer->name,
                'cycle_duration_days' => $salesDo->created_at->diffInDays(now()),
            ]);

            Log::info('Sales order cycle completed', [
                'sales_do_id' => $salesDo->id,
                'do_code' => $salesDo->do_code,
                'payment_id' => $payment->id,
            ]);

        } catch (\Throwable $e) {
            Log::error('Failed to close sales order cycle', [
                'sales_do_id' => $salesDo->id ?? null,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
