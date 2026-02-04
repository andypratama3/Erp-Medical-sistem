<?php

namespace App\Listeners;

use App\Events\DeliveryCompleted;
use App\Models\TaskBoard;
use Illuminate\Support\Facades\Log;

/**
 * Create ACT task board when delivery is completed
 */
class CreateACTTaskBoard
{
    public function handle(DeliveryCompleted $event): void
    {
        $salesDo = $event->salesDo;

        try {
            // Prevent duplicate ACT invoicing task
            $exists = TaskBoard::where('sales_do_id', $salesDo->id)
                ->where('branch_id', $salesDo->branch_id)
                ->where('module', 'act')
                ->where('task_type', 'act_invoicing')
                ->exists();

            if ($exists) {
                return;
            }

            TaskBoard::create([
                'sales_do_id'      => $salesDo->id,
                'branch_id'        => $salesDo->branch_id,
                'module'           => 'act',
                'task_type'        => 'act_invoicing',
                'task_status'      => 'pending',
                'task_description' => 'Create invoice for DO ' . $salesDo->do_code,
                'priority'         => 'high',
                'due_date'         => now()->addHours(6), // Invoice should be created quickly
                'created_by'       => auth()->id() ?? $salesDo->created_by,
            ]);

        } catch (\Throwable $e) {
            Log::error('Failed to create ACT task board', [
                'sales_do_id' => $salesDo->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
