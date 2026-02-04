<?php

namespace App\Http\Controllers\FIN;

use App\Http\Controllers\Controller;
use App\Models\ACTInvoice;
use App\Models\FINCollection;
use App\Services\DocumentUploadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CollectionController extends Controller
{
    protected $documentService;

    public function __construct(DocumentUploadService $documentService)
    {
        $this->documentService = $documentService;
    }

    public function index()
    {
        $collections = FINCollection::with(['invoice.salesDo.customer'])
            ->latest()
            ->paginate(15);

        return view('pages.fin.collections.index', compact('collections'));
    }

    public function create(Request $request)
    {
        $invoice = ACTInvoice::with(['salesDo.customer'])
            ->findOrFail($request->invoice_id);

        if ($invoice->payment_status === 'paid') {
            return redirect()->route('fin.task-board.index')
                ->with('error', 'This invoice is already paid');
        }

        return view('pages.fin.collections.create', compact('invoice'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'invoice_id' => 'required|exists:act_invoices,id',
            'collection_date' => 'required|date',
            'amount_collected' => 'required|numeric|min:0',
            'payment_method' => 'required|in:cash,transfer,check,giro',
            'reference_number' => 'nullable|string',
            'payment_proof' => 'nullable|file|mimes:pdf,jpg,png|max:10240',
            'notes' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $invoice = ACTInvoice::findOrFail($validated['invoice_id']);

            // Validate amount
            if ($validated['amount_collected'] > $invoice->outstanding_amount) {
                return back()->with('error', 'Amount collected exceeds outstanding amount')->withInput();
            }

            // Create Collection
            $collection = FINCollection::create([
                'invoice_id' => $invoice->id,
                'collection_date' => $validated['collection_date'],
                'amount_collected' => $validated['amount_collected'],
                'payment_method' => $validated['payment_method'],
                'reference_number' => $validated['reference_number'] ?? null,
                'notes' => $validated['notes'] ?? null,
                'collected_by' => auth()->id(),
            ]);

            // Upload payment proof
            if ($request->hasFile('payment_proof')) {
                $this->documentService->upload(
                    $request->file('payment_proof'),
                    FINCollection::class,
                    $collection->id,
                    'fin_payment_proof',
                    'Payment proof'
                );
            }

            // Update invoice outstanding
            $newOutstanding = $invoice->outstanding_amount - $validated['amount_collected'];
            $invoice->update([
                'outstanding_amount' => $newOutstanding,
                'payment_status' => $newOutstanding <= 0 ? 'paid' : 'partial',
            ]);

            // If fully paid, update DO status and complete task
            if ($newOutstanding <= 0) {
                $invoice->salesDo->update(['status' => 'fin_paid']);
                $invoice->salesDo->taskBoards()->where('module', 'fin')->update(['task_status' => 'completed']);
            } else {
                $invoice->salesDo->update(['status' => 'fin_on_collect']);
                $invoice->salesDo->taskBoards()->where('module', 'fin')->update(['task_status' => 'in_progress']);
            }

            DB::commit();

            return redirect()->route('fin.collections.show', $collection)
                ->with('success', 'Collection recorded successfully');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Failed to record collection: ' . $e->getMessage())->withInput();
        }
    }

    public function show(FINCollection $collection)
    {
        $collection->load(['invoice.salesDo.customer', 'documents']);
        return view('pages.fin.collections.show', compact('collection'));
    }

    public function recordPayment(Request $request, ACTInvoice $invoice)
    {
        $validated = $request->validate([
            'payment_date' => 'required|date',
            'amount' => 'required|numeric|min:0',
            'payment_method' => 'required|in:cash,transfer,check,giro',
            'reference_number' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            // Create payment record
            $payment = FINPayment::create([
                'invoice_id' => $invoice->id,
                'branch_id' => $invoice->branch_id,
                'payment_date' => $validated['payment_date'],
                'amount' => $validated['amount'],
                'payment_method' => $validated['payment_method'],
                'reference_number' => $validated['reference_number'] ?? null,
                'notes' => $validated['notes'] ?? null,
                'status' => 'confirmed',
                'created_by' => Auth::id(),
            ]);

            // Update invoice status
            $remainingAmount = $invoice->total_amount - $invoice->payments->sum('amount');

            if ($remainingAmount <= 0) {
                // Fully paid
                $invoice->update([
                    'status' => 'paid',
                    'paid_at' => now(),
                ]);

                // Update Sales DO status
                $invoice->salesDO->update(['status' => 'fin_paid']);

            } else {
                // Partially paid
                $invoice->update([
                    'status' => 'partial',
                ]);
            }

            $this->auditLog->log('PAYMENT_RECORDED', 'FIN', [
                'payment_id' => $payment->id,
                'invoice_number' => $invoice->invoice_number,
                'amount' => $payment->amount,
                'payment_method' => $payment->payment_method,
            ]);

            // *** ADD THIS: Dispatch PaymentReceived event (only for full payment) ***
            if ($invoice->status === 'paid') {
                event(new PaymentReceived($payment));
            }

            DB::commit();

            return redirect()
                ->route('fin.collections.show', $invoice)
                ->with('success', 'Payment recorded successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()
                ->back()
                ->with('error', 'Failed to record payment: ' . $e->getMessage());
        }
    }

    public function startCollection(Request $request, ACTInvoice $invoice)
    {
        if ($invoice->status !== 'approved') {
            return redirect()
                ->back()
                ->with('error', 'Invoice must be approved before starting collection.');
        }

        DB::beginTransaction();
        try {
            $invoice->update([
                'collection_started_at' => now(),
                'collection_assigned_to' => Auth::id(),
            ]);

            // Update Sales DO status
            $invoice->salesDO->update(['status' => 'fin_on_collect']);

            $this->auditLog->log('COLLECTION_STARTED', 'FIN', [
                'invoice_id' => $invoice->id,
                'invoice_number' => $invoice->invoice_number,
                'assigned_to' => Auth::user()->name,
            ]);

            DB::commit();

            return redirect()
                ->back()
                ->with('success', 'Collection process started.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()
                ->back()
                ->with('error', 'Failed to start collection: ' . $e->getMessage());
        }
    }

    public function markOverdue(Request $request, ACTInvoice $invoice)
    {
        $validated = $request->validate([
            'notes' => 'nullable|string',
        ]);

        // Check if invoice is actually overdue
        if ($invoice->due_date && $invoice->due_date->isFuture()) {
            return redirect()
                ->back()
                ->with('error', 'Invoice is not yet due.');
        }

        DB::beginTransaction();
        try {
            $invoice->update([
                'status' => 'overdue',
                'overdue_notes' => $validated['notes'] ?? null,
            ]);

            // Update Sales DO status
            $invoice->salesDO->update(['status' => 'fin_overdue']);

            $this->auditLog->log('INVOICE_OVERDUE', 'FIN', [
                'invoice_id' => $invoice->id,
                'invoice_number' => $invoice->invoice_number,
                'days_overdue' => now()->diffInDays($invoice->due_date),
            ]);

            // *** ADD THIS: Dispatch PaymentOverdue event ***
            event(new PaymentOverdue($invoice));

            DB::commit();

            return redirect()
                ->back()
                ->with('warning', 'Invoice marked as overdue.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()
                ->back()
                ->with('error', 'Failed to mark invoice as overdue: ' . $e->getMessage());
        }
    }


}
