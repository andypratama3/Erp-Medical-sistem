<?php

namespace App\Http\Controllers\CRM;

use App\Http\Controllers\Controller;
use App\Models\SalesDO;
use App\Models\SalesDOItem;
use App\Models\Customer;
use App\Models\MasterOffice;
use App\Models\Product;
use App\Models\Tax;
use App\Models\PaymentTerm;
use App\Services\DONumberGenerator;
use App\Services\AuditLogService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\DB;

class SalesDOController extends Controller implements HasMiddleware
{
    protected $doNumberGenerator;
    protected $auditLog;

    public function __construct(DONumberGenerator $doNumberGenerator, AuditLogService $auditLog)
    {
        $this->doNumberGenerator = $doNumberGenerator;
        $this->auditLog = $auditLog;
    }

    public static function middleware(): array
    {
        return [
            new Middleware('permission:view_sales_do', only: ['index', 'show']),
            new Middleware('permission:create_sales_do', only: ['create', 'store']),
            new Middleware('permission:edit_sales_do', only: ['edit', 'update']),
            new Middleware('permission:delete_sales_do', only: ['destroy']),
        ];
    }

    /**
     * Display a listing of Sales DO
     */
    public function index(Request $request)
    {
        $query = SalesDO::with([
            'customer',
            'office',
            'tax',
            'paymentTerm',
            'createdBy'
        ]);

        /* ============================
        FILTERS
        ============================ */
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('do_code', 'like', "%{$search}%")
                ->orWhere('tracking_code', 'like', "%{$search}%")
                ->orWhereHas('customer', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('office_id')) {
            $query->where('office_id', $request->office_id);
        }

        if ($request->filled('customer_id')) {
            $query->where('customer_id', $request->customer_id);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('do_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('do_date', '<=', $request->date_to);
        }

        $salesDOs = $query->latest()->paginate(15);

        /* ============================
        TABLE COLUMNS
        ============================ */
        $columns = [
            ['key' => 'do_code', 'label' => 'DO Code', 'type' => 'text'],
            ['key' => 'do_date', 'label' => 'DO Date', 'type' => 'date'],
            ['key' => 'customer', 'label' => 'Customer', 'type' => 'text'],
            ['key' => 'office', 'label' => 'Office', 'type' => 'text'],
            ['key' => 'grand_total', 'label' => 'Grand Total', 'type' => 'text'],
            ['key' => 'status', 'label' => 'Status', 'type' => 'badge'],
        ];

        /* ============================
        FORMAT DATA FOR TABLE
        ============================ */




        $salesDOsData = $salesDOs->getCollection()->map(function ($do) {
        $status = $do->status_config;

        return [
            'id' => $do->id,
            'do_code' => $do->do_code,
            'do_date' => $do->do_date->format('d-m-Y'),
            'customer' => $do->customer?->name ?? '-',
            'office' => $do->office?->name ?? '-',
            'grand_total' => $do->grand_total,

            'status' => [
                'value' => $do->status,
                'label' => $status['label'],
                'color' => $status['color'],
                'badge_class' => $status['badge_class'],
            ],

            'actions' => [
                'show' => route('crm.sales-do.show', $do),
                'edit' => $do->canBeEdited() ? route('crm.sales-do.edit', $do) : null,
                'delete' => $do->canBeDeleted() ? route('crm.sales-do.destroy', $do) : null,
            ],
        ];
    })->toArray();


        /* ============================
        FILTER DATA
        ============================ */
        $offices = MasterOffice::active()->get();
        $customers = Customer::active()->get();
        $statuses = [
            'crm_to_wqs' => 'CRM to WQS',
            'wqs_ready' => 'WQS Ready',
            'wqs_on_hold' => 'WQS On Hold',
            'scm_on_delivery' => 'On Delivery',
            'scm_delivered' => 'Delivered',
            'act_tukar_faktur' => 'Tukar Faktur',
            'act_invoiced' => 'Invoiced',
            'fin_on_collect' => 'On Collection',
            'fin_paid' => 'Paid',
            'fin_overdue' => 'Overdue',
        ];

        return view('pages.crm.sales_do.index', compact(
            'columns',
            'salesDOs',
            'salesDOsData',
            'offices',
            'customers',
            'statuses'
        ));
    }


    /**
     * Show the form for creating a new Sales DO
     */
    public function create()
    {
        $customers = Customer::active()->get();
        $offices = MasterOffice::active()->get();
        $products = Product::active()->get();
        $taxes = Tax::active()->get();
        $paymentTerms = PaymentTerm::active()->get();

        return view('pages.crm.sales_do.create', compact(
            'customers',
            'offices',
            'products',
            'taxes',
            'paymentTerms'
        ));
    }

    /**
     * Store a newly created Sales DO in storage
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:master_customers,id',
            'office_id' => 'required|exists:master_offices,id',
            'do_date' => 'required|date',
            'pic_customer' => 'nullable|string|max:100',
            'shipping_address' => 'required|string',
            'payment_term_id' => 'nullable|exists:master_payment_terms,id',
            'tax_id' => 'nullable|exists:master_tax,id',
            'notes_crm' => 'nullable|string',

            // Items
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:master_products,id',
            'items.*.qty_ordered' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.discount_percent' => 'nullable|numeric|min:0|max:100',
        ]);

        DB::beginTransaction();
        try {
            // Generate DO number
            $office = MasterOffice::find($validated['office_id']);
            $doCode = $this->doNumberGenerator->generate($office->code, $validated['do_date']);

            // Calculate totals
            $subtotal = 0;
            foreach ($validated['items'] as $item) {
                $lineTotal = $item['qty_ordered'] * $item['unit_price'];
                $discountAmount = $lineTotal * (($item['discount_percent'] ?? 0) / 100);
                $subtotal += ($lineTotal - $discountAmount);
            }

            $taxAmount = 0;
            if ($validated['tax_id']) {
                $tax = Tax::find($validated['tax_id']);
                $taxAmount = $subtotal * ($tax->rate / 100);
            }

            $grandTotal = $subtotal + $taxAmount;

            // Create Sales DO
            $salesDO = SalesDO::create([
                'do_code' => $doCode,
                'tracking_code' => $doCode,
                'customer_id' => $validated['customer_id'],
                'office_id' => $validated['office_id'],
                'do_date' => $validated['do_date'],
                'pic_customer' => $validated['pic_customer'],
                'shipping_address' => $validated['shipping_address'],
                'payment_term_id' => $validated['payment_term_id'],
                'tax_id' => $validated['tax_id'],
                'subtotal' => $subtotal,
                'tax_amount' => $taxAmount,
                'grand_total' => $grandTotal,
                'status' => 'crm_to_wqs',
                'notes_crm' => $validated['notes_crm'],
                'created_by' => auth()->id(),
            ]);

            // Create DO Items
            foreach ($validated['items'] as $index => $item) {
                $product = Product::find($item['product_id']);
                $lineTotal = $item['qty_ordered'] * $item['unit_price'];
                $discountAmount = $lineTotal * (($item['discount_percent'] ?? 0) / 100);

                SalesDOItem::create([
                    'sales_do_id' => $salesDO->id,
                    'product_id' => $item['product_id'],
                    'line_number' => $index + 1,
                    'product_sku' => $product->sku,
                    'product_name' => $product->name,
                    'unit' => $product->unit,
                    'qty_ordered' => $item['qty_ordered'],
                    'qty_delivered' => 0,
                    'unit_price' => $item['unit_price'],
                    'discount_percent' => $item['discount_percent'] ?? 0,
                    'discount_amount' => $discountAmount,
                    'line_total' => $lineTotal - $discountAmount,
                ]);
            }

            // Audit log
            $this->auditLog->log('SALES_DO_CREATED', 'CRM', [
                'do_code' => $doCode,
                'customer_id' => $validated['customer_id'],
                'grand_total' => $grandTotal,
            ]);

            DB::commit();

            return redirect()
                ->route('crm.sales-do.show', $salesDO)
                ->with('success', 'Sales DO created successfully. DO Number: ' . $doCode);

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Failed to create Sales DO: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified Sales DO
     */
    public function show(SalesDO $salesDo)
    {
        $salesDo->load([
            'customer',
            'office',
            'tax',
            'paymentTerm',
            'items.product',
            'createdBy',
            'updatedBy',
            'documents',
            'taskBoards'
        ]);

        return view('pages.crm.sales_do.show', compact('salesDo'));
    }

    /**
     * Show the form for editing the specified Sales DO
     */
    public function edit(SalesDO $salesDo)
    {
        // Only allow edit if status is still crm_to_wqs or wqs_on_hold
        if (!in_array($salesDo->status, ['crm_to_wqs', 'wqs_on_hold'])) {
            return redirect()
                ->route('crm.sales-do.show', $salesDo)
                ->with('error', 'Cannot edit DO with status: ' . $salesDo->status);
        }

        $salesDo->load('items');
        $customers = Customer::active()->get();
        $offices = MasterOffice::active()->get();
        $products = Product::active()->get();
        $taxes = Tax::active()->get();
        $paymentTerms = PaymentTerm::active()->get();

        return view('pages.crm.sales_do.edit', compact(
            'salesDo',
            'customers',
            'offices',
            'products',
            'taxes',
            'paymentTerms'
        ));
    }

    /**
     * Update the specified Sales DO in storage
     */
    public function update(Request $request, SalesDO $salesDo)
    {
        // Check if editable
        if (!in_array($salesDo->status, ['crm_to_wqs', 'wqs_on_hold'])) {
            return redirect()
                ->route('crm.sales-do.show', $salesDo)
                ->with('error', 'Cannot edit DO with status: ' . $salesDo->status);
        }

        $validated = $request->validate([
            'customer_id' => 'required|exists:master_customers,id',
            'office_id' => 'required|exists:master_offices,id',
            'do_date' => 'required|date',
            'pic_customer' => 'nullable|string|max:100',
            'shipping_address' => 'required|string',
            'payment_term_id' => 'nullable|exists:master_payment_terms,id',
            'tax_id' => 'nullable|exists:master_tax,id',
            'notes_crm' => 'nullable|string',

            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:master_products,id',
            'items.*.qty_ordered' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.discount_percent' => 'nullable|numeric|min:0|max:100',
        ]);

        DB::beginTransaction();
        try {
            // Recalculate totals
            $subtotal = 0;
            foreach ($validated['items'] as $item) {
                $lineTotal = $item['qty_ordered'] * $item['unit_price'];
                $discountAmount = $lineTotal * (($item['discount_percent'] ?? 0) / 100);
                $subtotal += ($lineTotal - $discountAmount);
            }

            $taxAmount = 0;
            if ($validated['tax_id']) {
                $tax = Tax::find($validated['tax_id']);
                $taxAmount = $subtotal * ($tax->rate / 100);
            }

            $grandTotal = $subtotal + $taxAmount;

            // Update Sales DO
            $salesDo->update([
                'customer_id' => $validated['customer_id'],
                'office_id' => $validated['office_id'],
                'do_date' => $validated['do_date'],
                'pic_customer' => $validated['pic_customer'],
                'shipping_address' => $validated['shipping_address'],
                'payment_term_id' => $validated['payment_term_id'],
                'tax_id' => $validated['tax_id'],
                'subtotal' => $subtotal,
                'tax_amount' => $taxAmount,
                'grand_total' => $grandTotal,
                'notes_crm' => $validated['notes_crm'],
                'updated_by' => auth()->id(),
            ]);

            // Delete old items and recreate
            $salesDo->items()->delete();

            foreach ($validated['items'] as $index => $item) {
                $product = Product::find($item['product_id']);
                $lineTotal = $item['qty_ordered'] * $item['unit_price'];
                $discountAmount = $lineTotal * (($item['discount_percent'] ?? 0) / 100);

                SalesDOItem::create([
                    'sales_do_id' => $salesDo->id,
                    'product_id' => $item['product_id'],
                    'line_number' => $index + 1,
                    'product_sku' => $product->sku,
                    'product_name' => $product->name,
                    'unit' => $product->unit,
                    'qty_ordered' => $item['qty_ordered'],
                    'qty_delivered' => 0,
                    'unit_price' => $item['unit_price'],
                    'discount_percent' => $item['discount_percent'] ?? 0,
                    'discount_amount' => $discountAmount,
                    'line_total' => $lineTotal - $discountAmount,
                ]);
            }

            // Audit log
            $this->auditLog->log('SALES_DO_UPDATED', 'CRM', [
                'do_code' => $salesDo->do_code,
                'grand_total' => $grandTotal,
            ]);

            DB::commit();

            return redirect()
                ->route('crm.sales-do.show', $salesDo)
                ->with('success', 'Sales DO updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Failed to update Sales DO: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified Sales DO from storage (soft delete)
     */
    public function destroy(SalesDO $salesDo)
    {
        // Only allow delete if status is crm_to_wqs
        if ($salesDo->status !== 'crm_to_wqs') {
            return redirect()
                ->back()
                ->with('error', 'Cannot delete DO with status: ' . $salesDo->status);
        }

        $salesDo->delete();

        // Audit log
        $this->auditLog->log('SALES_DO_DELETED', 'CRM', [
            'do_code' => $salesDo->do_code,
        ]);

        return redirect()
            ->route('crm.sales-do.index')
            ->with('success', 'Sales DO deleted successfully.');
    }

    /**
     * Export Sales DO to PDF
     */
    public function exportPDF(SalesDO $salesDo)
    {
        $salesDo->load(['customer', 'office', 'items.product', 'tax', 'paymentTerm']);

        $pdf = app('dompdf.wrapper');
        $pdf->loadView('pages.crm.sales_do.pdf', compact('salesDo'));

        return $pdf->download('DO-' . $salesDo->do_code . '.pdf');
    }

    public function submit(SalesDO $salesDo)
    {
        // Check permission
        if (!auth()->user()->can('submit_sales_do')) {
            return redirect()
                ->back()
                ->with('error', 'You do not have permission to submit Sales DO.');
        }

        // Only allow submit if status is crm_to_wqs
        if ($salesDo->status !== 'crm_to_wqs') {
            return redirect()
                ->back()
                ->with('error', 'Cannot submit DO with status: ' . $salesDo->status . '. Only CRM to WQS status can be submitted.');
        }

        // Validate all items have proper data
        $salesDo->load('items');

        if ($salesDo->items->isEmpty()) {
            return redirect()
                ->back()
                ->with('error', 'Cannot submit DO with no items.');
        }

        foreach ($salesDo->items as $item) {
            if ($item->qty_ordered <= 0 || $item->qty_ordered === null) {
                return redirect()
                    ->back()
                    ->with('error', 'All items must have valid quantity ordered.');
            }

            if ($item->unit_price <= 0 || $item->unit_price === null) {
                return redirect()
                    ->back()
                    ->with('error', 'All items must have valid unit price.');
            }
        }

        // Validate customer and office
        if (!$salesDo->customer_id || !$salesDo->office_id) {
            return redirect()
                ->back()
                ->with('error', 'Customer and Office must be selected.');
        }

        // Validate required shipping address
        if (!$salesDo->shipping_address) {
            return redirect()
                ->back()
                ->with('error', 'Shipping address is required.');
        }

        DB::beginTransaction();
        try {
            // Update status
            $salesDo->update([
                'status' => 'wqs_ready',
                'submitted_by' => auth()->id(),
                'submitted_at' => now(),
                'updated_by' => auth()->id(),
            ]);

            // Log to audit trail
            $this->auditLog->log('SALES_DO_SUBMITTED', 'CRM', [
                'do_code' => $salesDo->do_code,
                'customer_id' => $salesDo->customer_id,
                'grand_total' => $salesDo->grand_total,
                'submitted_by' => auth()->user()->name,
            ]);

            // Dispatch event for WQS module to listen (optional)
            // event(new SalesDOSubmitted($salesDo));

            DB::commit();

            return redirect()
                ->route('crm.sales-do.show', $salesDo)
                ->with('success', 'Sales DO submitted successfully to WQS. Status changed to WQS Ready.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()
                ->back()
                ->with('error', 'Failed to submit Sales DO: ' . $e->getMessage());
        }
    }
}
