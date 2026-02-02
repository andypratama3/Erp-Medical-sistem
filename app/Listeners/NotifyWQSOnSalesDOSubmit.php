<?php

namespace App\Listeners;

use App\Events\SalesDOSubmitted;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Log;

/**
 * Listener that notifies WQS team when a Sales DO is submitted
 *
 * Sends notifications to all WQS users in the same branch
 */
class NotifyWQSOnSalesDOSubmit
{
    protected NotificationService $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Handle the event.
     */
    public function handle(SalesDOSubmitted $event): void
    {
        $salesDo = $event->salesDo;

        try {
            $wqsUsers = User::role('wqs')
                ->where('current_branch_id', $salesDo->branch_id ?? 1)
                ->get();

            if ($wqsUsers->isEmpty()) {
                Log::warning('No WQS users found', [
                    'branch_id' => $salesDo->branch_id,
                    'sales_do_id' => $salesDo->id,
                ]);
                return;
            }

            foreach ($wqsUsers as $user) {
                $this->notificationService->send([
                    'user_id' => $user->id,
                    'type' => 'sales_do_submitted',
                    'title' => 'New Sales DO Ready for Stock Check',
                    'message' => sprintf(
                        'Sales DO %s has been submitted by %s. Customer: %s. Total: Rp %s.',
                        $salesDo->do_code,
                        $salesDo->createdBy->name ?? 'Unknown',
                        $salesDo->customer->name,
                        number_format($salesDo->grand_total, 0, ',', '.')
                    ),
                    'url' => route('wqs.task-board.show', $salesDo),
                    'data' => [
                        'sales_do_id' => $salesDo->id,
                        'do_code' => $salesDo->do_code,
                        'customer_name' => $salesDo->customer->name,
                        'total_amount' => $salesDo->grand_total,
                        'item_count' => $salesDo->items->count(),
                    ],
                ]);
            }

        } catch (\Throwable $e) {
            Log::error('Failed to notify WQS team', [
                'sales_do_id' => $salesDo->id ?? null,
                'error' => $e->getMessage(),
            ]);
        }
    }

}
