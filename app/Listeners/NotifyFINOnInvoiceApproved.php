<?php

namespace App\Listeners;

use App\Events\InvoiceApproved;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Log;

/**
 * Notify finance team when invoice is approved
 */
class NotifyFINOnInvoiceApproved
{
    protected NotificationService $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function handle(InvoiceApproved $event): void
    {
        $invoice = $event->invoice;
        $salesDo = $event->salesDo;

        try {
            $finUsers = User::role('finance')
                ->orWhereHas('roles', fn($q) => $q->where('name', 'fin'))
                ->where('current_branch_id', $invoice->branch_id)
                ->get();

            if ($finUsers->isEmpty()) {
                Log::warning('No FIN users found', [
                    'branch_id' => $invoice->branch_id,
                    'invoice_id' => $invoice->id,
                ]);
                return;
            }

            foreach ($finUsers as $user) {
                $this->notificationService->send([
                    'user_id' => $user->id,
                    'type' => 'invoice_approved',
                    'title' => 'Invoice Ready for Collection',
                    'message' => sprintf(
                        'Invoice %s has been approved. Customer: %s. Amount: Rp %s. Due: %s',
                        $invoice->invoice_number,
                        $invoice->customer->name,
                        number_format($invoice->total_amount, 0, ',', '.'),
                        $invoice->due_date?->format('d M Y') ?? 'N/A'
                    ),
                    'url' => route('fin.task-board.show', $salesDo),
                    'data' => [
                        'invoice_id' => $invoice->id,
                        'invoice_number' => $invoice->invoice_number,
                        'due_date' => $invoice->due_date,
                        'total_amount' => $invoice->total_amount,
                    ],
                ]);
            }

        } catch (\Throwable $e) {
            Log::error('Failed to notify FIN team', [
                'invoice_id' => $invoice->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
