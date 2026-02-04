<?php

namespace App\Listeners;

use App\Events\DeliveryCompleted;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Log;

/**
 * Notify accounting team when delivery is completed
 */
class NotifyACTOnDeliveryComplete
{
    protected NotificationService $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function handle(DeliveryCompleted $event): void
    {
        $salesDo = $event->salesDo;
        $delivery = $event->delivery;

        try {
            $actUsers = User::role('accounting')
                ->orWhereHas('roles', fn($q) => $q->where('name', 'act'))
                ->where('current_branch_id', $salesDo->branch_id)
                ->get();

            if ($actUsers->isEmpty()) {
                Log::warning('No ACT users found', [
                    'branch_id' => $salesDo->branch_id,
                    'sales_do_id' => $salesDo->id,
                ]);
                return;
            }

            foreach ($actUsers as $user) {
                $this->notificationService->send([
                    'user_id' => $user->id,
                    'type' => 'delivery_completed',
                    'title' => 'Delivery Completed - Create Invoice',
                    'message' => sprintf(
                        'DO %s has been delivered. Customer: %s. Total: Rp %s. Please create invoice.',
                        $salesDo->do_code,
                        $salesDo->customer->name,
                        number_format($salesDo->grand_total, 0, ',', '.')
                    ),
                    'url' => route('act.task-board.show', $salesDo),
                    'data' => [
                        'sales_do_id' => $salesDo->id,
                        'do_code' => $salesDo->do_code,
                        'delivery_id' => $delivery->id,
                        'pod_uploaded' => $delivery->pod_file ? true : false,
                    ],
                ]);
            }

        } catch (\Throwable $e) {
            Log::error('Failed to notify ACT team', [
                'sales_do_id' => $salesDo->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
