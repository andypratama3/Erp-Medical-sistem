<?php

namespace App\Listeners;

use App\Events\StockCheckFailed;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Log;

/**
 * Notify procurement/purchasing when stock check fails
 */
class NotifyStockShortage
{
    protected NotificationService $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function handle(StockCheckFailed $event): void
    {
        $stockCheck = $event->stockCheck;
        $salesDo = $event->salesDo;
        $problematicItems = $event->problematicItems;

        try {
            // Notify purchasing/procurement team
            $procurementUsers = User::role('purchasing')
                ->orWhereHas('roles', fn($q) => $q->where('name', 'procurement'))
                ->where('current_branch_id', $salesDo->branch_id)
                ->get();

            foreach ($procurementUsers as $user) {
                $this->notificationService->send([
                    'user_id' => $user->id,
                    'type' => 'stock_shortage',
                    'title' => '⚠️ Stock Shortage Detected',
                    'message' => sprintf(
                        'DO %s has insufficient stock. %d items need restocking.',
                        $salesDo->do_code,
                        count($problematicItems)
                    ),
                    'url' => route('wqs.stock-checks.show', $stockCheck),
                    'data' => [
                        'stock_check_id' => $stockCheck->id,
                        'sales_do_id' => $salesDo->id,
                        'problematic_items' => $problematicItems,
                    ],
                ]);
            }

            // Notify WQS team
            $wqsUsers = User::role('wqs')
                ->where('current_branch_id', $salesDo->branch_id)
                ->get();

            foreach ($wqsUsers as $user) {
                $this->notificationService->send([
                    'user_id' => $user->id,
                    'type' => 'stock_check_failed',
                    'title' => 'Stock Check Failed',
                    'message' => sprintf(
                        'DO %s stock check failed. Order is on hold pending stock availability.',
                        $salesDo->do_code
                    ),
                    'url' => route('wqs.stock-checks.problematic-items', $stockCheck),
                    'data' => [
                        'stock_check_id' => $stockCheck->id,
                        'failed_items_count' => count($problematicItems),
                    ],
                ]);
            }

            Log::info('Stock shortage notifications sent', [
                'stock_check_id' => $stockCheck->id,
                'sales_do_id' => $salesDo->id,
                'problematic_items_count' => count($problematicItems),
            ]);

        } catch (\Throwable $e) {
            Log::error('Failed to notify stock shortage', [
                'stock_check_id' => $stockCheck->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
