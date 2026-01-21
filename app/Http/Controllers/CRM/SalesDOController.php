<?php

namespace App\Http\Controllers\CRM;

use App\Http\Controllers\Controller;
use App\Models\SalesDO;
use App\Models\Customer;
use App\Models\MasterOffice;
use App\Models\Product;
use App\Services\DONumberGenerator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SalesDOController extends Controller
{
    protected $doNumberGenerator;

    public function __construct(DONumberGenerator $doNumberGenerator)
    {
        $this->doNumberGenerator = $doNumberGenerator;
    }

    public function index()
    {
        $salesDOs = SalesDO::with(['customer', 'office'])
            ->latest()
            ->paginate(15);

        return view('pages.crm.sales_do.index', compact('salesDOs'));
    }

    public function create()
    {
        $customers = Customer::active()->get();
        $offices = MasterOffice::active()->get();
        $products = Product::active()->get();

        return view('pages.crm.sales_do.create', compact('customers', 'offices', 'products'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'do_date' => 'required|date',
            'customer_id' => 'required|exists:master_customers,id',
            'office_id' => 'required|exists:master_offices,id',
            'customer_address' => 'nullable',
            'customer_phone' => 'nullable',
            'customer_pic' => 'nullable',
            'payment_term_id' => 'nullable|exists:master_payment_terms,id',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:master_products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'notes' => 'nullable',
        ]);

        DB::beginTransaction();
        try {
            // Generate DO Number
            $doNumber = $this->doNumberGenerator->generate();

            // Create Sales DO
            $salesDO = SalesDO::create([
                'do_number' => $doNumber,
                'do_date' => $validated['do_date'],
                'customer_id' => $validated['customer_id'],
                'office_id' => $validated['office_id'],
                'customer_address' => $validated['customer_address'],
                'customer_phone' => $validated['customer_phone'],
                'customer_pic' => $validated['customer_pic'],
                'payment_term_id' => $validated['payment_term_id'],
                'status' => 'crm_to_wqs',
                'notes' => $validated['notes'] ?? null,
                'created_by' => auth()->id(),
            ]);

            // Create DO Items
            foreach ($validated['items'] as $item) {
                $product = Product::find($item['product_id']);

                $salesDO->items()->create([
                    'product_id' => $item['product_id'],
                    'product_name' => $product->name,
                    'unit' => $product->unit,
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'discount_percent' => $item['discount_percent'] ?? 0,
                    'discount_amount' => 0,
                    'subtotal' => 0,
                    'tax_amount' => 0,
                    'total' => 0,
                ]);
            }

            // Calculate totals
            $salesDO->calculateTotals();

            DB::commit();

            return redirect()->route('crm.sales-do.show', $salesDO)
                ->with('success', 'Sales DO created successfully');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Failed to create Sales DO: ' . $e->getMessage())->withInput();
        }
    }

    public function show(SalesDO $salesDo)
    {
        $salesDo->load(['customer', 'office', 'items.product', 'documents']);
        return view('pages.crm.sales_do.show', compact('salesDo'));
    }

    public function submit(SalesDO $salesDo)
    {
        if ($salesDo->status !== 'crm_to_wqs') {
            return back()->with('error', 'DO cannot be submitted from current status');
        }

        // Transition to WQS
        $salesDo->update(['status' => 'wqs_ready']);

        // Create WQS Task
        $salesDo->taskBoards()->create([
            'module' => 'wqs',
            'task_status' => 'pending',
            'task_description' => 'Stock check for DO ' . $salesDo->do_number,
            'due_date' => now()->addDays(1),
        ]);

        return redirect()->route('crm.sales-do.show', $salesDo)
            ->with('success', 'DO submitted to WQS successfully');
    }
}
