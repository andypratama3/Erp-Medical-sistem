<?php
namespace App\Http\Controllers\WQS;

use App\Http\Controllers\Controller;
use App\Models\SalesDO;
use App\Models\WQSStockCheck;
use App\Services\DocumentUploadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StockCheckController extends Controller
{
    protected $documentService;

    public function __construct(DocumentUploadService $documentService)
    {
        $this->documentService = $documentService;
    }

    public function index()
    {
        $stockChecks = WQSStockCheck::with(['salesDo.customer'])
            ->latest()
            ->paginate(15);

        return view('pages.wqs.stock_checks.index', compact('stockChecks'));
    }

    public function create(Request $request)
    {
        $salesDo = SalesDO::with(['items.product', 'customer'])
            ->findOrFail($request->do_id);

        if ($salesDo->status !== 'wqs_ready') {
            return redirect()->route('wqs.task-board')
                ->with('error', 'This DO is not ready for stock check');
        }

        return view('pages.wqs.stock_checks.create', compact('salesDo'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'sales_do_id' => 'required|exists:sales_do,id',
            'check_date' => 'nullable|date',
            'check_notes' => 'required|string',
            'items' => 'required|array',
            'items.*.product_id' => 'required|exists:master_products,id',
            'items.*.stock_status' => 'required|in:available,partial,not_available',
            'items.*.available_qty' => 'required|integer|min:0',
            'items.*.stock_photo' => 'nullable|image|max:5120',
        ]);

        DB::beginTransaction();
        try {
            $salesDo = SalesDO::findOrFail($validated['sales_do_id']);

            // Create Stock Check
            $stockCheck = WQSStockCheck::create([
                'sales_do_id' => $salesDo->id,
                'check_date' => $validated['check_date'] ?? now(),
                'check_notes' => $validated['check_notes'],
                'checked_by' => auth()->id(),
                'overall_status' => 'checked',
            ]);

            // Process each item
            foreach ($validated['items'] as $itemData) {
                // Upload stock photo if provided
                if (isset($itemData['stock_photo'])) {
                    $this->documentService->upload(
                        $itemData['stock_photo'],
                        WQSStockCheck::class,
                        $stockCheck->id,
                        'wqs_stock_photo',
                        'Stock photo for product ' . $itemData['product_id']
                    );
                }

                // Update stock check details (if you have a detail table)
                // Or store in JSON field, etc.
            }

            // Update DO status
            $allAvailable = collect($validated['items'])->every(fn($item) => $item['stock_status'] === 'available');
            
            if ($allAvailable) {
                $salesDo->update(['status' => 'scm_ready']);
                
                // Create SCM Task
                $salesDo->taskBoards()->create([
                    'module' => 'scm',
                    'task_status' => 'pending',
                    'task_description' => 'Delivery for DO ' . $salesDo->do_number,
                    'due_date' => now()->addDays(2),
                ]);
            }

            // Update WQS task
            $salesDo->taskBoards()->where('module', 'wqs')->update(['task_status' => 'completed']);

            DB::commit();

            return redirect()->route('wqs.task-board')
                ->with('success', 'Stock check completed successfully');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Failed to complete stock check: ' . $e->getMessage())->withInput();
        }
    }

    public function show(WQSStockCheck $stockCheck)
    {
        $stockCheck->load(['salesDo.customer', 'salesDo.items.product', 'documents']);
        return view('pages.wqs.stock_checks.show', compact('stockCheck'));
    }
}