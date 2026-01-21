<?php

namespace App\Http\Controllers\WQS;

use App\Http\Controllers\Controller;
use App\Models\SalesDO;
use App\Models\TaskBoard;
use App\Models\WQSStockCheck;
use App\Models\DocumentUpload;
use App\Services\DocumentUploadService;
use App\Services\AuditLogService;
use App\Services\StateMachineService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\DB;

class TaskBoardController extends Controller implements HasMiddleware
{
    protected $documentUpload;
    protected $auditLog;
    protected $stateMachine;

    public function __construct(
        DocumentUploadService $documentUpload,
        AuditLogService $auditLog,
        StateMachineService $stateMachine
    ) {
        $this->documentUpload = $documentUpload;
        $this->auditLog = $auditLog;
        $this->stateMachine = $stateMachine;
    }

    public static function middleware(): array
    {
        return [
            new Middleware('permission:view_wqs', only: ['index', 'show']),
            new Middleware('permission:process_wqs', only: ['process', 'ready', 'hold']),
        ];
    }

    /**
     * Display WQS Task Board
     */
    public function index(Request $request)
    {
        $query = SalesDO::with(['customer', 'office', 'items'])
            ->whereIn('status', ['crm_to_wqs', 'wqs_ready', 'wqs_on_hold']);

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('do_code', 'like', "%{$search}%")
                  ->orWhereHas('customer', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $salesDOs = $query->latest('do_date')->paginate(15);

        // Get statistics
        $stats = [
            'pending' => SalesDO::where('status', 'crm_to_wqs')->count(),
            'ready' => SalesDO::where('status', 'wqs_ready')->count(),
            'on_hold' => SalesDO::where('status', 'wqs_on_hold')->count(),
        ];

        return view('pages.wqs.task_board.index', compact('salesDOs', 'stats'));
    }

    /**
     * Show detailed task for WQS processing
     */
    public function show(SalesDO $salesDo)
    {
        // Check if DO is in WQS stage
        if (!in_array($salesDo->status, ['crm_to_wqs', 'wqs_ready', 'wqs_on_hold'])) {
            return redirect()
                ->route('wqs.task-board.index')
                ->with('error', 'This DO is not in WQS stage.');
        }

        $salesDo->load([
            'customer',
            'office',
            'items.product',
            'stockChecks',
            'documents' => function($query) {
                $query->where('stage', 'wqs_stok');
            }
        ]);

        return view('pages.wqs.task_board.show', compact('salesDo'));
    }

    /**
     * Process WQS task (stock check and ready)
     */
    public function process(Request $request, SalesDO $salesDo)
    {
        $validated = $request->validate([
            'notes_wqs' => 'nullable|string',
            'stock_checks' => 'required|array',
            'stock_checks.*.product_id' => 'required|exists:master_products,id',
            'stock_checks.*.stock_available' => 'required|boolean',
            'stock_checks.*.stock_qty' => 'required|integer|min:0',
            'stock_checks.*.notes' => 'nullable|string',
            'photos_before.*' => 'nullable|image|max:2048',
            'photos_after.*' => 'nullable|image|max:2048',
        ]);

        DB::beginTransaction();
        try {
            // Save stock checks
            foreach ($validated['stock_checks'] as $index => $check) {
                WQSStockCheck::create([
                    'sales_do_id' => $salesDo->id,
                    'product_id' => $check['product_id'],
                    'stock_available' => $check['stock_available'],
                    'stock_qty' => $check['stock_qty'],
                    'checked_by' => auth()->id(),
                    'notes' => $check['notes'] ?? null,
                ]);
            }

            // Upload photos
            if ($request->hasFile('photos_before')) {
                foreach ($request->file('photos_before') as $photo) {
                    $this->documentUpload->upload(
                        'sales_do',
                        $salesDo->do_code,
                        'wqs_stok_before',
                        $photo
                    );
                }
            }

            if ($request->hasFile('photos_after')) {
                foreach ($request->file('photos_after') as $photo) {
                    $this->documentUpload->upload(
                        'sales_do',
                        $salesDo->do_code,
                        'wqs_stok_after',
                        $photo
                    );
                }
            }

            // Update DO notes
            $salesDo->update([
                'notes_wqs' => $validated['notes_wqs'],
                'updated_by' => auth()->id(),
            ]);

            // Audit log
            $this->auditLog->log('WQS_STOCK_CHECKED', 'WQS', [
                'do_code' => $salesDo->do_code,
                'checked_items' => count($validated['stock_checks']),
            ]);

            DB::commit();

            return redirect()
                ->route('wqs.task-board.show', $salesDo)
                ->with('success', 'Stock check completed successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()
                ->back()
                ->with('error', 'Failed to process: ' . $e->getMessage());
        }
    }

    /**
     * Mark DO as ready for SCM
     */
    public function ready(SalesDO $salesDo)
    {
        // Validate state transition
        if (!$this->stateMachine->canTransition('sales_do', $salesDo->status, 'wqs_ready')) {
            return redirect()
                ->back()
                ->with('error', 'Invalid status transition.');
        }

        // Check if all items have been stock checked
        $checkedCount = $salesDo->stockChecks()->count();
        $itemsCount = $salesDo->items()->count();

        if ($checkedCount < $itemsCount) {
            return redirect()
                ->back()
                ->with('error', 'Please complete stock check for all items first.');
        }

        $salesDo->update([
            'status' => 'wqs_ready',
            'updated_by' => auth()->id(),
        ]);

        // Audit log
        $this->auditLog->log('WQS_MARKED_READY', 'WQS', [
            'do_code' => $salesDo->do_code,
        ]);

        return redirect()
            ->route('wqs.task-board.index')
            ->with('success', 'DO marked as ready for delivery.');
    }

    /**
     * Hold DO (stock issue)
     */
    public function hold(Request $request, SalesDO $salesDo)
    {
        $validated = $request->validate([
            'hold_reason' => 'required|string',
        ]);

        $salesDo->update([
            'status' => 'wqs_on_hold',
            'notes_wqs' => ($salesDo->notes_wqs ?? '') . "\n\nHOLD: " . $validated['hold_reason'],
            'updated_by' => auth()->id(),
        ]);

        // Audit log
        $this->auditLog->log('WQS_MARKED_HOLD', 'WQS', [
            'do_code' => $salesDo->do_code,
            'reason' => $validated['hold_reason'],
        ]);

        return redirect()
            ->route('wqs.task-board.index')
            ->with('warning', 'DO placed on hold.');
    }
}

// ============================================
// FILE: app/Http/Controllers/WQS/StockCheckController.php
// ============================================

namespace App\Http\Controllers\WQS;

use App\Http\Controllers\Controller;
use App\Models\WQSStockCheck;
use App\Models\SalesDO;
use App\Models\Product;
use Illuminate\Http\Request;

class StockCheckController extends Controller
{
    /**
     * Store stock check result
     */
    public function store(Request $request, SalesDO $salesDo)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:master_products,id',
            'stock_available' => 'required|boolean',
            'stock_qty' => 'required|integer|min:0',
            'notes' => 'nullable|string',
        ]);

        $stockCheck = WQSStockCheck::create([
            'sales_do_id' => $salesDo->id,
            'product_id' => $validated['product_id'],
            'stock_available' => $validated['stock_available'],
            'stock_qty' => $validated['stock_qty'],
            'checked_by' => auth()->id(),
            'notes' => $validated['notes'],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Stock check recorded.',
            'data' => $stockCheck
        ]);
    }

    /**
     * Update stock check
     */
    public function update(Request $request, WQSStockCheck $stockCheck)
    {
        $validated = $request->validate([
            'stock_available' => 'required|boolean',
            'stock_qty' => 'required|integer|min:0',
            'notes' => 'nullable|string',
        ]);

        $stockCheck->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Stock check updated.',
        ]);
    }

    /**
     * Delete stock check
     */
    public function destroy(WQSStockCheck $stockCheck)
    {
        $stockCheck->delete();

        return response()->json([
            'success' => true,
            'message' => 'Stock check deleted.',
        ]);
    }
}
