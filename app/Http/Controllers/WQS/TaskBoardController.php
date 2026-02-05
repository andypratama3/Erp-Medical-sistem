<?php

namespace App\Http\Controllers\WQS;

use App\Http\Controllers\Controller;
use App\Models\SalesDO;
use App\Models\TaskBoard;
use App\Models\WQSStockCheck;
use App\Services\AuditLogService;
use App\Services\StateMachineService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\DB;

class TaskBoardController extends Controller implements HasMiddleware
{
    protected $auditLog;
    protected $stateMachine;

    public function __construct(
        AuditLogService $auditLog,
        StateMachineService $stateMachine
    ) {
        $this->auditLog = $auditLog;
        $this->stateMachine = $stateMachine;
    }

    public static function middleware(): array
    {
        return [
            new Middleware('permission:wqs.view', only: ['index', 'show','view']),
            new Middleware('permission:wqs.process', only: ['process', 'start', 'hold', 'complete']),
        ];
    }

    /**
     * Display WQS Task Board
     */
    public function index(Request $request)
    {
        $query = TaskBoard::with([
            'salesDO.customer',
            'salesDO.office',
            'assignedUser',
        ])
        ->byModule('wqs')
        ->notCompleted();

        /* ============================
        FILTERS
        ============================ */
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('task_description', 'like', "%{$search}%")
                  ->orWhereHas('salesDO', function($q) use ($search) {
                      $q->where('do_code', 'like', "%{$search}%")
                        ->orWhereHas('customer', function($q) use ($search) {
                            $q->where('name', 'like', "%{$search}%");
                        });
                  });
            });
        }

        if ($request->filled('status')) {
            $query->byStatus($request->status);
        }

        if ($request->filled('priority')) {
            $query->byPriority($request->priority);
        }

        if ($request->filled('assigned_to')) {
            $query->assignedTo($request->assigned_to);
        }

        /* ============================
        SORTING & PAGINATION
        ============================ */
        $sortBy = $request->get('sort', 'due_date');
        $sortOrder = $request->get('order', 'asc');

        if ($sortBy === 'priority') {
            $query->orderByRaw("FIELD(priority, 'urgent', 'high', 'medium', 'low')");
        } else {
            $query->orderBy($sortBy, $sortOrder);
        }

        $tasks = $query->paginate(15);

        /* ============================
        STATISTICS
        ============================ */
        $stats = TaskBoard::getDashboardStats('wqs');
        $stats['total'] = $stats['pending'] + $stats['in_progress'] + $stats['on_hold'];


        return view('pages.wqs.task_board.index', compact('tasks', 'stats'));
    }

    /**
     * Show detailed task for WQS processing
     */
    public function show($id)
    {
        $taskBoard = TaskBoard::findOrFail($id);
        // Verify task is WQS module
        if ($taskBoard->module !== 'wqs') {
            return redirect()
                ->route('wqs.task-board.index')
                ->with('error', 'Invalid task.');
        }

        $taskBoard->load([
            'salesDO.customer',
            'salesDO.office',
            'salesDO.items.product',
            'assignedUser',
            'documents',
        ]);

        // Load related stock check if exists
        $stockCheck = WQSStockCheck::where('sales_do_id', $taskBoard->sales_do_id)
                                   ->latest()
                                   ->first();

        if ($stockCheck) {
            $stockCheck->load('items.product');
        }

        return view('pages.wqs.task_board.show', compact('taskBoard', 'stockCheck'));
    }

    /**
     * Start processing task
     */
    public function start(TaskBoard $taskBoard)
    {
        if (!$taskBoard->canStart()) {
            return redirect()
                ->back()
                ->with('error', 'Task cannot be started from current status.');
        }

        if (!$taskBoard->start()) {
            return redirect()
                ->back()
                ->with('error', 'Failed to start task.');
        }

        $this->auditLog->log('TASK_STARTED', 'WQS', [
            'task_id' => $taskBoard->id,
            'do_code' => $taskBoard->salesDO->do_code,
            'task_type' => $taskBoard->task_type,
        ]);


        // Make task Board to SCM Task Board AND invoice
        // (new WQSStockCheck())->start($taskBoard);

        return redirect()
            ->route('wqs.task-board.show', $taskBoard)
            ->with('success', 'Task started.');
    }

    /**
     * Hold task with reason
     */
    public function hold(Request $request, TaskBoard $taskBoard)
    {
        $validated = $request->validate([
            'reason' => 'required|string|max:255',
        ]);

        if (!$taskBoard->canHold()) {
            return redirect()
                ->back()
                ->with('error', 'Task cannot be held from current status.');
        }

        if (!$taskBoard->hold($validated['reason'])) {
            return redirect()
                ->back()
                ->with('error', 'Failed to hold task.');
        }

        // Update related SO status if needed
        if ($taskBoard->task_type === 'wqs_stock_check') {
            $taskBoard->salesDO->update(['status' => 'wqs_on_hold']);
        }

        $this->auditLog->log('TASK_HELD', 'WQS', [
            'task_id' => $taskBoard->id,
            'do_code' => $taskBoard->salesDO->do_code,
            'reason' => $validated['reason'],
        ]);

        return redirect()
            ->back()
            ->with('warning', 'Task placed on hold: ' . $validated['reason']);
    }

    /**
     * Resume task from hold
     */
    public function resume(TaskBoard $taskBoard)
    {
        if (!$taskBoard->canResume()) {
            return redirect()
                ->back()
                ->with('error', 'Task cannot be resumed from current status.');
        }

        if (!$taskBoard->resume()) {
            return redirect()
                ->back()
                ->with('error', 'Failed to resume task.');
        }

        $this->auditLog->log('TASK_RESUMED', 'WQS', [
            'task_id' => $taskBoard->id,
            'do_code' => $taskBoard->salesDO->do_code,
        ]);

        return redirect()
            ->route('wqs.task-board.show', $taskBoard)
            ->with('success', 'Task resumed.');
    }

    /**
     * Complete task (after stock check)
     */
    public function complete(Request $request, TaskBoard $taskBoard)
    {
        $validated = $request->validate([
            'notes' => 'nullable|string',
        ]);

        if (!$taskBoard->canComplete()) {
            return redirect()
                ->back()
                ->with('error', 'Task cannot be completed from current status.');
        }

        // For stock check task, verify stock check is completed
        if ($taskBoard->task_type === 'wqs_stock_check') {
            $stockCheck = WQSStockCheck::where('sales_do_id', $taskBoard->sales_do_id)
                                       ->where('overall_status', 'completed')
                                       ->exists();

            if (!$stockCheck) {
                return redirect()
                    ->back()
                    ->with('error', 'Please complete stock check first.');
            }
        }

        DB::beginTransaction();
        try {
            if (!$taskBoard->complete()) {
                throw new \Exception('Failed to complete task.');
            }

            // Update notes if provided
            if ($validated['notes']) {
                $taskBoard->update(['notes' => ($taskBoard->notes ?? '') . "\nCOMPLETED: " . $validated['notes']]);
            }

            // Handle status transition for DO
            $this->handleStatusTransition($taskBoard);

            $this->auditLog->log('TASK_COMPLETED', 'WQS', [
                'task_id' => $taskBoard->id,
                'do_code' => $taskBoard->salesDO->do_code,
                'task_type' => $taskBoard->task_type,
            ]);

            // *** ADD THIS: Dispatch WQSCompleted event ***
            event(new WQSCompleted($taskBoard->salesDO));

            DB::commit();

            return redirect()
                ->route('wqs.task-board.index')
                ->with('success', 'Task completed successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()
                ->back()
                ->with('error', 'Failed to complete task: ' . $e->getMessage());
        }
    }

    /**
     * Reject task
     */
    public function reject(Request $request, TaskBoard $taskBoard)
    {
        $validated = $request->validate([
            'reason' => 'required|string|max:255',
        ]);

        // if (!$taskBoard->canHold()) {
        //     return redirect()
        //         ->back()
        //         ->with('error', 'Task cannot be rejected from current status.');
        // }

        DB::beginTransaction();
        try {
            if (!$taskBoard->reject($validated['reason'])) {
                throw new \Exception('Failed to reject task.');
            }

            // Update DO status to previous stage
            $taskBoard->salesDO->update(['status' => 'crm_to_wqs']);

            $this->auditLog->log('TASK_REJECTED', 'WQS', [
                'task_id' => $taskBoard->id,
                'do_code' => $taskBoard->salesDO->do_code,
                'reason' => $validated['reason'],
            ]);

            DB::commit();

            return redirect()
                ->route('wqs.task-board.index')
                ->with('error', 'Task rejected: ' . $validated['reason']);

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()
                ->back()
                ->with('error', 'Failed to reject task: ' . $e->getMessage());
        }
    }

    /**
     * Handle status transition based on task completion
     */
    private function handleStatusTransition(TaskBoard $taskBoard): void
    {
        $salesDo = $taskBoard->salesDO;

        switch ($taskBoard->task_type) {
            case 'wqs_stock_check':
                // Check if all items are available
                $stockCheck = WQSStockCheck::where('sales_do_id', $salesDo->id)
                                           ->latest()
                                           ->first();

                if ($stockCheck && $stockCheck->allAvailable()) {
                    // All items available - proceed to SCM
                    $salesDo->update(['status' => 'scm_on_delivery']);

                    // Create SCM task
                    TaskBoard::create([
                        'sales_do_id' => $salesDo->id,
                        'module' => 'scm',
                        'task_type' => 'scm_pick_pack',
                        'task_status' => 'pending',
                        'task_description' => 'Pick & Pack for DO ' . $salesDo->do_code,
                        'priority' => 'high',
                        'due_date' => now()->addDays(1),
                        'created_by' => auth()->id(),
                    ]);
                } else {
                    // Some items not available - place on hold
                    $salesDo->update(['status' => 'wqs_on_hold']);
                }
                break;

            case 'wqs_quality_review':
                // Quality review done - ready for SCM
                $salesDo->update(['status' => 'scm_on_delivery']);

                // Create SCM delivery task
                TaskBoard::create([
                    'sales_do_id' => $salesDo->id,
                    'module' => 'scm',
                    'task_type' => 'scm_delivery',
                    'task_status' => 'pending',
                    'task_description' => 'Delivery for DO ' . $salesDo->do_code,
                    'priority' => 'high',
                    'due_date' => now()->addDays(2),
                    'created_by' => auth()->id(),
                ]);
                break;
        }
    }

    /**
     * Assign task to user
     */
    public function assign(Request $request, TaskBoard $taskBoard)
    {
        $validated = $request->validate([
            'assigned_to' => 'required|exists:users,id',
        ]);

        $taskBoard->update([
            'assigned_to' => $validated['assigned_to'],
            'updated_by' => auth()->id(),
        ]);

        $this->auditLog->log('TASK_ASSIGNED', 'WQS', [
            'task_id' => $taskBoard->id,
            'assigned_to' => $validated['assigned_to'],
        ]);

        return redirect()
            ->back()
            ->with('success', 'Task assigned successfully.');
    }

    /**
     * Update task priority
     */
    public function updatePriority(Request $request, TaskBoard $taskBoard)
    {
        $validated = $request->validate([
            'priority' => 'required|in:low,medium,high,urgent',
        ]);

        $taskBoard->update([
            'priority' => $validated['priority'],
            'updated_by' => auth()->id(),
        ]);

        $this->auditLog->log('TASK_PRIORITY_UPDATED', 'WQS', [
            'task_id' => $taskBoard->id,
            'priority' => $validated['priority'],
        ]);

        return redirect()
            ->back()
            ->with('success', 'Priority updated.');
    }

    /**
     * Get dashboard stats via AJAX
     */
    public function dashboardStats()
    {
        $stats = TaskBoard::getDashboardStats('wqs');
        $stats['total'] = $stats['pending'] + $stats['in_progress'] + $stats['on_hold'];


        return response()->json([
            'success' => true,
            'data' => $stats,
        ]);
    }
}
