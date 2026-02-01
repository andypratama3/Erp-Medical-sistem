<?php

namespace App\Http\Controllers\ACT;

use App\Models\SalesDO;
use App\Models\Customer;
use App\Models\ACTInvoice;
use Illuminate\Http\Request;
use App\Services\AuditLogService;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class InvoiceController extends Controller
{
    protected $auditLog;

    public function __construct(AuditLogService $auditLog)
    {
        $this->auditLog = $auditLog;
    }


    public function index(Request $request)
    {
        $query = ACTInvoice::with(['customer', 'salesDO', 'branch', 'createdBy']);

        /* ============================
        FILTERS
        ============================ */
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                ->orWhereHas('customer', fn ($c) =>
                        $c->where('name', 'like', "%{$search}%")
                );
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('invoice_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('invoice_date', '<=', $request->date_to);
        }

        $invoices = $query->latest()->paginate(15);

        /* ============================
        TABLE COLUMNS (KEY BASED)
        ============================ */
        $columns = [
            ['key' => 'invoice_number', 'label' => 'Invoice Number', 'type' => 'text'],
            ['key' => 'customer', 'label' => 'Customer', 'type' => 'text'],
            ['key' => 'invoice_date', 'label' => 'Invoice Date', 'type' => 'text'],
            ['key' => 'due_date', 'label' => 'Due Date', 'type' => 'text'],
            ['key' => 'total_amount', 'label' => 'Total Amount', 'type' => 'text'],
            ['key' => 'status', 'label' => 'Status', 'type' => 'badge'],
        ];

        /* ============================
        FORMAT DATA FOR TABLE
        ============================ */
        $invoicesData = $invoices->getCollection()->map(function ($invoice) {
            return [
                'id' => $invoice->id,

                'invoice_number' => $invoice->invoice_number,
                'customer'       => $invoice->customer?->name ?? '-',
                'invoice_date'   => $invoice->invoice_date?->format('d M Y') ?? '-',
                'due_date'       => $invoice->due_date?->format('d M Y') ?? '-',
                'total_amount'   => 'Rp ' . number_format($invoice->total_amount, 0, ',', '.'),

                'status' => [
                    'value' => $invoice->status,
                    'label' => ucfirst($invoice->status),
                    'color' => match ($invoice->status) {
                        'paid'     => 'success',
                        'unpaid'   => 'warning',
                        'overdue'  => 'danger',
                        default    => 'gray',
                    },
                ],

                'actions' => [
                    'show'   => route('act.invoices.show', $invoice),
                    'edit'   => route('act.invoices.edit', $invoice),
                    'delete' => route('act.invoices.destroy', $invoice),
                ],
            ];
        })->toArray();

        return view('pages.act.invoices.index', compact(
            'columns',
            'invoices',
            'invoicesData'
        ));
    }


    /**
     * Show the form for creating a new invoice
     */
    public function create()
    {
        try {
            $salesOrders = SalesDO::where('status', 'approved')
                ->where('branch_id', Auth::user()->current_branch_id)
                ->get();

            $customers = Customer::where('branch_id', Auth::user()->current_branch_id)
                ->get();

            return view('pages.act.invoices.create', compact('salesOrders', 'customers'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal membuka form: ' . $e->getMessage());
        }
    }

    /**
     * Store a newly created invoice in database
     */
    public function store(Request $request)
    {
        try {
            // Validate input
            $validated = $request->validate([
                'sales_do_id' => 'required|exists:sales_dos,id',
                'invoice_number' => 'required|unique:act_invoices',
                'invoice_date' => 'required|date',
                'due_date' => 'required|date|after:invoice_date',
                'customer_id' => 'required|exists:master_customers,id',
                'amount' => 'required|numeric|min:0',
                'tax_amount' => 'nullable|numeric|min:0',
                'notes' => 'nullable|string',
            ]);

            // Calculate total
            $validated['total_amount'] = $validated['amount'] + ($validated['tax_amount'] ?? 0);
            $validated['branch_id'] = Auth::user()->current_branch_id;
            $validated['created_by'] = Auth::id();
            $validated['status'] = 'draft';

            // Create invoice
            $invoice = ACTInvoice::create($validated);

            // Log audit
            $this->auditLog->log('CREATE', "Invoice {$invoice->invoice_number} created", Auth::id());

            return redirect()->route('invoices.show', $invoice)
                ->with('success', 'Invoice berhasil dibuat!');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput();
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified invoice
     */
    public function show(ACTInvoice $invoice)
    {
        try {
            $invoice->load(['salesDO', 'customer', 'payments', 'createdBy']);
            return view('pages.act.invoices.show', compact('invoice'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal mengambil data: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified invoice
     */
    public function edit(ACTInvoice $invoice)
    {
        try {
            if ($invoice->status !== 'draft') {
                return redirect()->back()->with('error', 'Hanya invoice draft yang bisa diedit!');
            }

            $salesOrders = SalesDO::where('status', 'approved')
                ->where('branch_id', Auth::user()->current_branch_id)
                ->get();

            $customers = Customer::where('branch_id', Auth::user()->current_branch_id)
                ->get();

            return view('pages.act.invoices.edit', compact('invoice', 'salesOrders', 'customers'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal membuka form: ' . $e->getMessage());
        }
    }

    /**
     * Update the specified invoice in database
     */
    public function update(Request $request, ACTInvoice $invoice)
    {
        try {
            if ($invoice->status !== 'draft') {
                return redirect()->back()->with('error', 'Hanya invoice draft yang bisa diubah!');
            }

            $validated = $request->validate([
                'invoice_number' => 'required|unique:act_invoices,invoice_number,' . $invoice->id,
                'invoice_date' => 'required|date',
                'due_date' => 'required|date|after:invoice_date',
                'customer_id' => 'required|exists:master_customers,id',
                'amount' => 'required|numeric|min:0',
                'tax_amount' => 'nullable|numeric|min:0',
                'notes' => 'nullable|string',
            ]);

            $validated['total_amount'] = $validated['amount'] + ($validated['tax_amount'] ?? 0);

            $invoice->update($validated);

            $this->auditLog->log('UPDATE', "Invoice {$invoice->invoice_number} updated", Auth::id());

            return redirect()->route('invoices.show', $invoice)
                ->with('success', 'Invoice berhasil diubah!');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput();
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified invoice
     */
    public function destroy(ACTInvoice $invoice)
    {
        try {
            if ($invoice->status !== 'draft') {
                return redirect()->back()->with('error', 'Hanya invoice draft yang bisa dihapus!');
            }

            $invoiceNumber = $invoice->invoice_number;
            $invoice->delete();

            $this->auditLog->log('DELETE', "Invoice {$invoiceNumber} deleted", Auth::id());

            return redirect()->route('invoices.index')
                ->with('success', 'Invoice berhasil dihapus!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menghapus: ' . $e->getMessage());
        }
    }

    /**
     * Approve invoice
     */
    public function approve(ACTInvoice $invoice)
    {
        try {
            if ($invoice->status !== 'draft') {
                return redirect()->back()->with('error', 'Invoice harus dalam status draft!');
            }

            $invoice->markAsApproved();
            $this->auditLog->log('APPROVE', "Invoice {$invoice->invoice_number} approved", Auth::id());

            return redirect()->back()->with('success', 'Invoice berhasil disetujui!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    /**
     * Cancel invoice
     */
    public function cancel(ACTInvoice $invoice)
    {
        try {
            if (in_array($invoice->status, ['paid', 'cancelled'])) {
                return redirect()->back()->with('error', 'Invoice tidak bisa dibatalkan!');
            }

            $invoice->markAsCancelled();
            $this->auditLog->log('CANCEL', "Invoice {$invoice->invoice_number} cancelled", Auth::id());

            return redirect()->back()->with('success', 'Invoice berhasil dibatalkan!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
        }
    }
}
