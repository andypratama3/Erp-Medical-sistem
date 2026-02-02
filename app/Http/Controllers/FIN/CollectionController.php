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
}
