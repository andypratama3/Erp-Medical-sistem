<?php

namespace App\Listeners;

use App\Events\InvoiceApproved;
use App\Models\TaskBoard;
use Illuminate\Support\Facades\Log;

/**
 * Create FIN task board when invoice is approved
 */
class CreateFINTaskBoard
{
    public function handle(InvoiceApproved $event): void
    {
        $invoice = $event->invoice;
        $salesDo = $event->salesDo;

        try {
            // Prevent duplicate FIN collection task
            $exists = TaskBoard::where('sales_do_id', $salesDo->id)
                ->where('branch_id', $salesDo->branch_id)
                ->where('module', 'fin')
                ->where('task_type', 'fin_collection')
                ->exists();

            if ($exists) {
                return;
            }

            TaskBoard::create([
                'sales_do_id'      => $salesDo->id,
                'branch_id'        => $salesDo->branch_id,
                'module'           => 'fin',
                'task_type'        => 'fin_collection',
                'task_status'      => 'pending',
                'task_description' => 'Collect payment for Invoice ' . $invoice->invoice_number,
                'priority'         => 'medium',
                'due_date'         => $invoice->due_date ?? now()->addDays(30),
                'created_by'       => auth()->id() ?? $invoice->created_by,
            ]);

        } catch (\Throwable $e) {
            Log::error('Failed to create FIN task board', [
                'invoice_id' => $invoice->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
