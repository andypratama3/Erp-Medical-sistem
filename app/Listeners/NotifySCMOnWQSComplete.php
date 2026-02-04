<?php

namespace App\Listeners;

use App\Events\WQSCompleted;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Log;

/**
 * Notify SCM team when WQS completes stock check
 */
class NotifySCMOnWQSComplete
{
    protected NotificationService $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function handle(WQSCompleted $event): void
    {
        $salesDo = $event->salesDo;

        try {
            $scmUsers = User::role('scm')
                ->where('current_branch_id', $salesDo->branch_id)
                ->get();

            if ($scmUsers->isEmpty()) {
                Log::warning('No SCM users found', [
                    'branch_id' => $salesDo->branch_id,
                    'sales_do_id' => $salesDo->id,
                ]);
                return;
            }

            foreach ($scmUsers as $user) {
                $this->notificationService->send([
                    'user_id' => $user->id,
                    'type' => 'wqs_completed',
                    'title' => 'Sales DO Ready for Delivery',
                    'message' => sprintf(
                        'DO %s has passed quality check. Customer: %s. Please arrange delivery.',
                        $salesDo->do_code,
                        $salesDo->customer->name
                    ),
                    'url' => route('scm.task-board.show', $salesDo),
                    'data' => [
                        'sales_do_id' => $salesDo->id,
                        'do_code' => $salesDo->do_code,
                        'customer_name' => $salesDo->customer->name,
                        'shipping_address' => $salesDo->shipping_address,
                    ],
                ]);
            }

        } catch (\Throwable $e) {
            Log::error('Failed to notify SCM team', [
                'sales_do_id' => $salesDo->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
