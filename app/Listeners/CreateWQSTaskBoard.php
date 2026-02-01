<?php

namespace App\Listeners;

use App\Events\SalesDOSubmitted;
use App\Models\TaskBoard;

class CreateWQSTaskBoard
{
    public function handle(SalesDOSubmitted $event): void
    {
        $salesDo = $event->salesDo;

        // Prevent duplicate WQS STOCK CHECK task
        $exists = TaskBoard::where('sales_do_id', $salesDo->id)
            ->where('branch_id', $salesDo->branch_id)
            ->where('module', 'wqs')
            ->where('task_type', 'wqs_stock_check')
            ->exists();

        if ($exists) {
            return;
        }

        TaskBoard::create([
            'sales_do_id'      => $salesDo->id,
            'branch_id'        => $salesDo->branch_id,
            'module'           => 'wqs',
            'task_type'        => 'wqs_stock_check',
            'task_status'      => 'pending',
            'task_description' => 'Stock & quality checking for DO ' . $salesDo->do_code,
            'priority'         => 'high',
            'due_date'         => now()->addDay(),
            'created_by'       => auth()->id() ?? $salesDo->created_by,
        ]);
    }
}
