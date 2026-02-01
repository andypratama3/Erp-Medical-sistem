<?php

namespace App\Http\Controllers\FIN;

use App\Http\Controllers\Controller;
use App\Models\{FINPayment, FINCollection, ACTInvoice};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $payments = FINPayment::with(['invoice', 'collection'])
            ->latest()
            ->paginate(20);

        return view('pages.fin.payments.index', compact('payments'));
    }

    public function create()
    {
        $unpaidInvoices = ACTInvoice::where('payment_status', 'unpaid')
            ->orWhere('payment_status', 'partial')
            ->with('salesDO.customer')
            ->get();

        return view('pages.fin.payments.create', compact('unpaidInvoices'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'invoice_id' => 'required|exists:act_invoices,id',
            'payment_date' => 'required|date',
            'payment_amount' => 'required|numeric|min:0',
            'payment_method' => 'required|in:cash,transfer,check,credit_card',
            'reference_number' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $invoice = ACTInvoice::findOrFail($validated['invoice_id']);

            // Create payment record
            $payment = FINPayment::create([
                'invoice_id' => $invoice->id,
                'payment_date' => $validated['payment_date'],
                'payment_amount' => $validated['payment_amount'],
                'payment_method' => $validated['payment_method'],
                'reference_number' => $validated['reference_number'],
                'notes' => $validated['notes'],
                'branch_id' => auth()->user()->current_branch_id,
            ]);

            // Update invoice payment status
            $totalPaid = $invoice->payments()->sum('payment_amount');

            if ($totalPaid >= $invoice->grand_total) {
                $invoice->update(['payment_status' => 'paid']);
            } else {
                $invoice->update(['payment_status' => 'partial']);
            }

            DB::commit();

            return redirect()
                ->route('fin.payments.show', $payment)
                ->with('success', 'Payment recorded successfully');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'Failed to record payment: ' . $e->getMessage());
        }
    }

    public function show(FINPayment $payment)
    {
        $payment->load(['invoice.salesDO.customer', 'collection']);

        return view('pages.fin.payments.show', compact('payment'));
    }
}
