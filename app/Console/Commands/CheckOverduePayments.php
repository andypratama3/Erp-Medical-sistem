<?php

namespace App\Console\Commands;

use App\Models\ACTInvoice;
use App\Events\PaymentOverdue;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CheckOverduePayments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fin:check-overdue-payments';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check for overdue invoices and dispatch PaymentOverdue events';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking for overdue payments...');

        // Get all unpaid invoices that are past due date
        $overdueInvoices = ACTInvoice::where('payment_status', 'unpaid')
            ->whereDate('due_date', '<', now())
            ->whereNull('overdue_notified_at') // Only notify once per day
            ->orWhere(function($query) {
                $query->where('payment_status', 'unpaid')
                      ->whereDate('due_date', '<', now())
                      ->whereDate('overdue_notified_at', '<', now()->startOfDay());
            })
            ->get();

        if ($overdueInvoices->isEmpty()) {
            $this->info('No overdue invoices found.');
            return 0;
        }

        $count = 0;
        foreach ($overdueInvoices as $invoice) {
            // Calculate days overdue
            $daysOverdue = now()->diffInDays($invoice->due_date, false);

            // Update invoice status to overdue
            $invoice->update([
                'payment_status' => 'overdue',
                'overdue_notified_at' => now(),
            ]);

            // Update Sales DO status
            $invoice->salesDO->update(['status' => 'fin_overdue']);

            // Dispatch overdue event
            event(new PaymentOverdue($invoice));

            $count++;

            Log::info('Overdue payment processed', [
                'invoice_id' => $invoice->id,
                'invoice_number' => $invoice->invoice_number,
                'days_overdue' => abs($daysOverdue),
            ]);
        }

        $this->info("Processed {$count} overdue invoices.");
        return 0;
    }
}
