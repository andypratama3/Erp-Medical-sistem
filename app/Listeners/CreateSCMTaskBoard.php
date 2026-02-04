<?php

namespace App\Listeners;

use App\Events\WQSCompleted;
use App\Models\TaskBoard;
use Illuminate\Support\Facades\Log;

/**
 * Create SCM task board when WQS completes
 */
class CreateSCMTaskBoard
{
    public function handle(WQSCompleted $event): void
    {
        $salesDo = $event->salesDo;

        try {
            // Prevent duplicate SCM delivery task
            $exists = TaskBoard::where('sales_do_id', $salesDo->id)
                ->where('branch_id', $salesDo->branch_id)
                ->where('module', 'scm')
                ->where('task_type', 'scm_delivery')
                ->exists();

            if ($exists) {
                return;
            }

            TaskBoard::create([
                'sales_do_id'      => $salesDo->id,
                'branch_id'        => $salesDo->branch_id,
                'module'           => 'scm',
                'task_type'        => 'scm_delivery',
                'task_status'      => 'pending',
                'task_description' => 'Arrange delivery for DO ' . $salesDo->do_code,
                'priority'         => 'high',
                'due_date'         => now()->addDay(),
                'created_by'       => auth()->id() ?? $salesDo->created_by,
            ]);

        } catch (\Throwable $e) {
            Log::error('Failed to create SCM task board', [
                'sales_do_id' => $salesDo->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
