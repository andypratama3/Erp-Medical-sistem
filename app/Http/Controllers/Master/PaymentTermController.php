<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\PaymentTerm;
use Illuminate\Http\Request;

class PaymentTermController extends Controller
{
    public function index(Request $request)
    {
        $query = PaymentTerm::query();

        // Filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                ->orWhere('name', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $paymentTerms = $query->latest()->paginate(15);

        /* ============================ 
        TABLE COLUMNS (KEY BASED)
        ============================ */
        $columns = [
            ['key' => 'code', 'label' => 'Code', 'type' => 'text'],
            ['key' => 'name', 'label' => 'Name', 'type' => 'text'],
            ['key' => 'days', 'label' => 'Days', 'type' => 'text'],
            ['key' => 'description', 'label' => 'Description', 'type' => 'text'],
            ['key' => 'status', 'label' => 'Status', 'type' => 'badge'],
        ];

        /* ============================ 
        FORMAT DATA FOR TABLE
        ============================ */
        $paymentTermsData = $paymentTerms->getCollection()->map(function ($paymentTerm) {
            return [
                'id' => $paymentTerm->id,
                'code' => $paymentTerm->code,
                'name' => $paymentTerm->name,
                'days' => $paymentTerm->days . ' days',
                'description' => $paymentTerm->description ?? '-',

                'status' => [
                    'value' => $paymentTerm->status,
                    'label' => ucfirst($paymentTerm->status),
                    'color' => match ($paymentTerm->status) {
                        'active' => 'active',
                        'inactive' => 'inactive',
                        default => 'gray',
                    }
                ],

                'actions' => [
                    'show' => route('master.payment-terms.show', $paymentTerm),
                    'edit' => route('master.payment-terms.edit', $paymentTerm),
                    'delete' => route('master.payment-terms.destroy', $paymentTerm),
                ],
            ];
        })->toArray();

        return view('pages.master.payment-terms.index', compact(
            'columns',
            'paymentTerms',
            'paymentTermsData'
        ));
    }

    public function create()
    {
        return view('pages.master.payment-terms.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:master_payment_terms,code',
            'name' => 'required|string|max:200',
            'days' => 'required|integer|min:0',
            'description' => 'nullable|string',
            'status' => 'required|in:active,inactive',
        ]);

        PaymentTerm::create($validated);

        return redirect()->route('master.payment-terms.index')
            ->with('success', 'Payment Term created successfully.');
    }

    public function show(PaymentTerm $paymentTerm)
    {
        return view('pages.master.payment-terms.show', compact('paymentTerm'));
    }

    public function edit(PaymentTerm $paymentTerm)
    {
        return view('pages.master.payment-terms.edit', compact('paymentTerm'));
    }

    public function update(Request $request, PaymentTerm $paymentTerm)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:master_payment_terms,code,' . $paymentTerm->id,
            'name' => 'required|string|max:200',
            'days' => 'required|integer|min:0',
            'description' => 'nullable|string',
            'status' => 'required|in:active,inactive',
        ]);

        $paymentTerm->update($validated);

        return redirect()->route('master.payment-terms.index')
            ->with('success', 'Payment Term updated successfully.');
    }

    public function destroy(PaymentTerm $paymentTerm)
    {
        $paymentTerm->delete();

        return redirect()->route('master.payment-terms.index')
            ->with('success', 'Payment Term deleted successfully.');
    }
}
