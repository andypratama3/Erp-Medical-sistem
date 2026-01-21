<?php

namespace App\Http\Controllers\ACT;

use App\Http\Controllers\Controller;
use App\Models\SalesDO;
use App\Models\ACTInvoice;
use App\Services\DocumentUploadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InvoiceController extends Controller
{
    protected $documentService;

    public function __construct(DocumentUploadService $documentService)
    {
        $this->documentService = $documentService;
    }

    public function index()
    {
        $invoices = ACTInvoice::with(['salesDo.customer'])
            ->latest()
            ->paginate(15);

        return view('pages.act.invoices.index', compact('invoices'));
    }

    public function create(Request $request)
    {
        $salesDo = SalesDO::with(['customer', 'items.product'])
            ->findOrFail($request->do_id);

        if (!in_array($salesDo->status, ['scm_delivered', 'act_tukar_faktur'])) {
            return redirect()->route('act.task-board')
                ->with('error', 'This DO is not ready for invoicing');
        }

        return view('pages.act.invoices.create', compact('salesDo'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'sales_do_id' => 'required|exists:sales_do,id',
            'invoice_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:invoice_date',
            'tax_percent' => 'nullable|numeric|min:0|max:100',
            'additional_charges' => 'nullable|numeric|min:0',
            'bank_name' => 'nullable|string',
            'account_number' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $salesDo = SalesDO::findOrFail($validated['sales_do_id']);

            // Generate invoice number
            $invoiceNumber = 'INV/' . date('Y/m') . '/' . str_pad(ACTInvoice::whereYear('created_at', date('Y'))->count() + 1, 4, '0', STR_PAD_LEFT);

            // Calculate amounts
            $subtotal = $salesDo->subtotal;
            $taxAmount = $subtotal * ($validated['tax_percent'] ?? 11) / 100;
            $totalAmount = $subtotal + $taxAmount + ($validated['additional_charges'] ?? 0);

            // Create Invoice
            $invoice = ACTInvoice::create([
                'sales_do_id' => $salesDo->id,
                'invoice_number' => $invoiceNumber,
                'invoice_date' => $validated['invoice_date'],
                'due_date' => $validated['due_date'],
                'subtotal_amount' => $subtotal,
                'tax_amount' => $taxAmount,
                'additional_charges' => $validated['additional_charges'] ?? 0,
                'total_amount' => $totalAmount,
                'outstanding_amount' => $totalAmount,
                'payment_status' => 'unpaid',
                'bank_name' => $validated['bank_name'] ?? null,
                'account_number' => $validated['account_number'] ?? null,
                'notes' => $validated['notes'] ?? null,
                'created_by' => auth()->id(),
            ]);

            // Update DO status
            $salesDo->update(['status' => 'act_invoiced']);

            // Update ACT task and create FIN task
            $salesDo->taskBoards()->where('module', 'act')->update(['task_status' => 'completed']);

            $salesDo->taskBoards()->create([
                'module' => 'fin',
                'task_status' => 'pending',
                'task_description' => 'Collection for invoice ' . $invoiceNumber,
                'due_date' => $validated['due_date'],
            ]);

            DB::commit();

            return redirect()->route('act.invoices.show', $invoice)
                ->with('success', 'Invoice generated successfully');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Failed to generate invoice: ' . $e->getMessage())->withInput();
        }
    }

    public function show(ACTInvoice $invoice)
    {
        $invoice->load(['salesDo.customer', 'salesDo.items.product', 'documents']);
        return view('pages.act.invoices.show', compact('invoice'));
    }

    public function uploadFaktur(Request $request, ACTInvoice $invoice)
    {
        $validated = $request->validate([
            'faktur_pajak' => 'required|file|mimes:pdf,jpg,png|max:10240',
            'faktur_number' => 'required|string',
        ]);

        DB::beginTransaction();
        try {
            // Upload faktur pajak
            $this->documentService->upload(
                $request->file('faktur_pajak'),
                ACTInvoice::class,
                $invoice->id,
                'act_faktur_pajak',
                'Faktur Pajak ' . $validated['faktur_number']
            );

            // Update invoice
            $invoice->update([
                'faktur_pajak_number' => $validated['faktur_number'],
            ]);

            DB::commit();

            return back()->with('success', 'Faktur Pajak uploaded successfully');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Failed to upload faktur: ' . $e->getMessage());
        }
    }
}
