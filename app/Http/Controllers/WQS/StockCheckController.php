<?php

namespace App\Http\Controllers\WQS;

use App\Http\Controllers\Controller;
use App\Models\SalesDO;
use App\Models\TaskBoard;
use App\Models\WQSStockCheck;
use App\Models\WQSStockCheckItem;
use App\Models\Product;
use App\Services\AuditLogService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\DB;

class StockCheckController extends Controller implements HasMiddleware
{
    protected $auditLog;

    public function __construct(AuditLogService $auditLog)
    {
        $this->auditLog = $auditLog;
    }

    public static function middleware(): array
    {
        return [
            new Middleware('permission:view_wqs', only: ['index', 'show']),
            new Middleware('permission:process_wqs', only: ['create', 'store', 'update', 'destroy']),
        ];
    }

    /**
     * Display stock checks list
     */
    public function index(Request $request)
    {
        $query = WQSStockCheck::with([
            'salesDO.customer',
            'salesDO.office',
            'checkedBy',
            'items',
        ]);

        /* ============================
        FILTERS
        ============================ */
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('salesDO', function($q) use ($search) {
                $q->where('do_code', 'like', "%{$search}%")
                  ->orWhereHas('customer', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('status')) {
            $query->byStatus($request->status);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('check_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('check_date', '<=', $request->date_to);
        }

        $stockChecks = $query->latest('check_date')->paginate(15);

        $tasks = WQSStockCheck::with([
            'salesDO.customer',
            'salesDO.office',
            'checkedBy',
            'items',
        ])
            ->byStatus('pending')
            ->latest('check_date')
            ->paginate(10);

        /* ============================
        STATISTICS
        ============================ */
        $stats = [
            'pending' => WQSStockCheck::pending()->count(),
            'checked' => WQSStockCheck::checked()->count(),
            'completed' => WQSStockCheck::completed()->count(),
            'failed' => WQSStockCheck::failed()->count(),
        ];

        return view('pages.wqs.stock_checks.index', compact('stockChecks','tasks','stats'));
    }

    /**
     * Show stock check form for DO
     */
    public function create(Request $request)
    {
        $salesDo = SalesDO::with(['items.product', 'customer', 'office'])
                         ->findOrFail($request->sales_do_id);

        // Verify DO is in WQS stage
        if (!in_array($salesDo->status, ['crm_to_wqs', 'wqs_ready', 'wqs_on_hold'])) {
            return redirect()
                ->route('wqs.task-board')
                ->with('error', 'This DO is not in WQS stage.');
        }

        // Check if stock check already exists
        $existingCheck = WQSStockCheck::where('sales_do_id', $salesDo->id)
                                      ->where('overall_status', '!=', 'failed')
                                      ->exists();

        if ($existingCheck) {
            return redirect()
                ->route('wqs.stock-checks.show', ['stock_check' => $existingCheck])
                ->with('info', 'Stock check already exists for this DO.');
        }

        return view('pages.wqs.stock_checks.create', compact('salesDo'));
    }

    /**
     * Store stock check record
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'sales_do_id' => 'required|exists:sales_do,id',
            'check_notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:master_products,id',
            'items.*.stock_status' => 'required|in:available,partial,not_available',
            'items.*.available_qty' => 'required|integer|min:0',
            'items.*.notes' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $salesDo = SalesDO::findOrFail($validated['sales_do_id']);

            // Create stock check record
            $stockCheck = WQSStockCheck::create([
                'sales_do_id' => $salesDo->id,
                'check_date' => now()->date(),
                'overall_status' => 'checked',
                'check_notes' => $validated['check_notes'],
                'checked_by' => auth()->id(),
            ]);

            // Add items to stock check
            foreach ($validated['items'] as $itemData) {
                WQSStockCheckItem::create([
                    'stock_check_id' => $stockCheck->id,
                    'product_id' => $itemData['product_id'],
                    'stock_status' => $itemData['stock_status'],
                    'available_qty' => $itemData['available_qty'],
                    'notes' => $itemData['notes'] ?? '',
                ]);
            }

            // Determine overall status
            $allAvailable = collect($validated['items'])
                ->every(fn($item) => $item['stock_status'] === 'available');

            if ($allAvailable) {
                $stockCheck->markCompleted();
                $salesDo->update(['status' => 'scm_on_delivery']);
                $status = 'completed';
            } else {
                $problematicItems = $stockCheck->getProblematicItems();
                $salesDo->update(['status' => 'wqs_on_hold']);
                $status = 'on_hold';
            }

            // Create/Update task
            $task = TaskBoard::where('sales_do_id', $salesDo->id)
                             ->where('module', 'wqs')
                             ->where('task_type', 'wqs_stock_check')
                             ->first();

            if ($task) {
                $task->update(['task_status' => 'completed']);
            } else {
                TaskBoard::create([
                    'sales_do_id' => $salesDo->id,
                    'module' => 'wqs',
                    'task_type' => 'wqs_stock_check',
                    'task_status' => 'completed',
                    'task_description' => 'Stock Check for DO ' . $salesDo->do_code,
                    'priority' => 'high',
                    'due_date' => now(),
                    'created_by' => auth()->id(),
                ]);
            }

            // Audit log
            $this->auditLog->log('WQS_STOCK_CHECK_CREATED', 'WQS', [
                'stock_check_id' => $stockCheck->id,
                'do_code' => $salesDo->do_code,
                'items_count' => count($validated['items']),
                'overall_status' => $status,
            ]);

            DB::commit();

            $message = $allAvailable
                ? 'Stock check completed - All items available. Proceeding to delivery.'
                : 'Stock check completed - Some items unavailable. DO placed on hold.';

            return redirect()
                ->route('wqs.stock-checks.show', $stockCheck)
                ->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()
                ->back()
                ->with('error', 'Failed to create stock check: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Show stock check details
     */
    public function show(WQSStockCheck $stockCheck)
    {
        $stockCheck->load([
            'salesDO.customer',
            'salesDO.office',
            'salesDO.items.product',
            'checkedBy',
            'items.product',
            'documents',
        ]);

        return view('pages.wqs.stock_checks.show', compact('stockCheck'));
    }

    /**
     * Edit stock check (if not completed)
     */
    public function edit(WQSStockCheck $stockCheck)
    {
        if ($stockCheck->overall_status === 'completed') {
            return redirect()
                ->route('wqs.stock-checks.show', $stockCheck)
                ->with('error', 'Cannot edit completed stock check.');
        }

        $stockCheck->load([
            'salesDO.items.product',
            'items.product',
        ]);

        return view('pages.wqs.stock_checks.edit', compact('stockCheck'));
    }

    /**
     * Update stock check record
     */
    public function update(Request $request, WQSStockCheck $stockCheck)
    {
        if ($stockCheck->overall_status === 'completed') {
            return redirect()
                ->back()
                ->with('error', 'Cannot update completed stock check.');
        }

        $validated = $request->validate([
            'check_notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.stock_check_item_id' => 'nullable|exists:wqs_stock_check_items,id',
            'items.*.product_id' => 'required|exists:master_products,id',
            'items.*.stock_status' => 'required|in:available,partial,not_available',
            'items.*.available_qty' => 'required|integer|min:0',
            'items.*.notes' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            // Update check notes
            $stockCheck->update([
                'check_notes' => $validated['check_notes'],
            ]);

            // Update items
            $existingItemIds = [];
            foreach ($validated['items'] as $itemData) {
                if ($itemData['stock_check_item_id']) {
                    // Update existing
                    $item = WQSStockCheckItem::findOrFail($itemData['stock_check_item_id']);
                    $item->update([
                        'stock_status' => $itemData['stock_status'],
                        'available_qty' => $itemData['available_qty'],
                        'notes' => $itemData['notes'] ?? '',
                    ]);
                    $existingItemIds[] = $item->id;
                } else {
                    // Create new
                    $item = WQSStockCheckItem::create([
                        'stock_check_id' => $stockCheck->id,
                        'product_id' => $itemData['product_id'],
                        'stock_status' => $itemData['stock_status'],
                        'available_qty' => $itemData['available_qty'],
                        'notes' => $itemData['notes'] ?? '',
                    ]);
                    $existingItemIds[] = $item->id;
                }
            }

            // Delete removed items
            $stockCheck->items()
                       ->whereNotIn('id', $existingItemIds)
                       ->delete();

            // Re-evaluate overall status
            $allAvailable = $stockCheck->items()
                ->where('stock_status', 'available')
                ->count() === $stockCheck->items()->count();

            if ($allAvailable && $stockCheck->isFullyChecked()) {
                $stockCheck->markCompleted();
                $stockCheck->salesDO->update(['status' => 'scm_on_delivery']);
            } else {
                $stockCheck->update(['overall_status' => 'checked']);
                $stockCheck->salesDO->update(['status' => 'wqs_on_hold']);
            }

            // Audit log
            $this->auditLog->log('WQS_STOCK_CHECK_UPDATED', 'WQS', [
                'stock_check_id' => $stockCheck->id,
                'do_code' => $stockCheck->salesDO->do_code,
            ]);

            DB::commit();

            return redirect()
                ->route('wqs.stock-checks.show', $stockCheck)
                ->with('success', 'Stock check updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()
                ->back()
                ->with('error', 'Failed to update stock check: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Mark stock check as failed
     */
    public function markFailed(Request $request, WQSStockCheck $stockCheck)
    {
        $validated = $request->validate([
            'reason' => 'required|string|max:255',
        ]);

        if ($stockCheck->overall_status === 'completed') {
            return redirect()
                ->back()
                ->with('error', 'Cannot fail completed stock check.');
        }

        $stockCheck->markFailed($validated['reason']);
        $stockCheck->salesDO->update(['status' => 'wqs_on_hold']);

        $this->auditLog->log('WQS_STOCK_CHECK_FAILED', 'WQS', [
            'stock_check_id' => $stockCheck->id,
            'do_code' => $stockCheck->salesDO->do_code,
            'reason' => $validated['reason'],
        ]);

        return redirect()
            ->back()
            ->with('error', 'Stock check marked as failed: ' . $validated['reason']);
    }

    /**
     * Delete stock check (only if not completed)
     */
    public function destroy(WQSStockCheck $stockCheck)
    {
        if ($stockCheck->overall_status === 'completed') {
            return redirect()
                ->back()
                ->with('error', 'Cannot delete completed stock check.');
        }

        $doCode = $stockCheck->salesDO->do_code;
        $stockCheck->delete();

        $this->auditLog->log('WQS_STOCK_CHECK_DELETED', 'WQS', [
            'do_code' => $doCode,
        ]);

        return redirect()
            ->route('wqs.stock-checks.index')
            ->with('success', 'Stock check deleted.');
    }

    /**
     * Get problematic items in stock check
     */
    public function getProblematicItems(WQSStockCheck $stockCheck)
    {
        $items = $stockCheck->getProblematicItems()
                           ->load('product')
                           ->map(function($item) {
                               return [
                                   'id' => $item->id,
                                   'product' => $item->product->name,
                                   'sku' => $item->product->sku,
                                   'stock_status' => $item->stock_status_label,
                                   'available_qty' => $item->available_qty,
                                   'notes' => $item->notes,
                                   'investigation' => $item->getInvestigationDetails(),
                               ];
                           });

        return response()->json([
            'success' => true,
            'data' => $items,
        ]);
    }

    /**
     * Generate stock check report
     */
    public function generateReport(WQSStockCheck $stockCheck)
    {
        $report = $stockCheck->getReport();

        return response()->json([
            'success' => true,
            'data' => $report,
        ]);
    }
}
