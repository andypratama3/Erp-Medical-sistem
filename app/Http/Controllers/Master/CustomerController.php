<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\PaymentTerm;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $query = Customer::with('paymentTerm');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%");
            });
        }

        $customers = $query->latest()->paginate(15);
        return view('pages.master.customers.index', compact('customers'));
    }

    public function create()
    {
        $paymentTerms = PaymentTerm::active()->get();
        return view('pages.master.customers.create', compact('paymentTerms'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|unique:master_customers|max:50',
            'name' => 'required|max:255',
            'legal_name' => 'nullable|max:255',
            'npwp' => 'nullable|max:50',
            'address' => 'nullable',
            'city' => 'nullable|max:100',
            'province' => 'nullable|max:100',
            'postal_code' => 'nullable|max:10',
            'phone' => 'nullable|max:20',
            'mobile' => 'nullable|max:20',
            'email' => 'nullable|email|max:100',
            'contact_person' => 'nullable|max:200',
            'contact_phone' => 'nullable|max:20',
            'payment_term_id' => 'nullable|exists:master_payment_terms,id',
            'credit_limit' => 'nullable|numeric|min:0',
            'credit_days' => 'nullable|integer|min:0',
            'customer_type' => 'required|in:hospital,clinic,pharmacy,distributor,retail,government,other',
            'status' => 'required|in:active,inactive,blocked',
            'notes' => 'nullable',
        ]);

        Customer::create($validated);

        return redirect()->route('master.customers.index')
            ->with('success', 'Customer created successfully');
    }

    public function show(Customer $customer)
    {
        $customer->load(['paymentTerm', 'salesDOs']);
        return view('pages.master.customers.show', compact('customer'));
    }

    public function edit(Customer $customer)
    {
        $paymentTerms = PaymentTerm::active()->get();
        return view('pages.master.customers.edit', compact('customer', 'paymentTerms'));
    }

    public function update(Request $request, Customer $customer)
    {
        $validated = $request->validate([
            'code' => 'required|max:50|unique:master_customers,code,' . $customer->id,
            'name' => 'required|max:255',
            'legal_name' => 'nullable|max:255',
            'npwp' => 'nullable|max:50',
            'address' => 'nullable',
            'city' => 'nullable|max:100',
            'province' => 'nullable|max:100',
            'postal_code' => 'nullable|max:10',
            'phone' => 'nullable|max:20',
            'mobile' => 'nullable|max:20',
            'email' => 'nullable|email|max:100',
            'contact_person' => 'nullable|max:200',
            'contact_phone' => 'nullable|max:20',
            'payment_term_id' => 'nullable|exists:master_payment_terms,id',
            'credit_limit' => 'nullable|numeric|min:0',
            'credit_days' => 'nullable|integer|min:0',
            'customer_type' => 'required',
            'status' => 'required|in:active,inactive,blocked',
            'notes' => 'nullable',
        ]);

        $customer->update($validated);

        return redirect()->route('master.customers.index')
            ->with('success', 'Customer updated successfully');
    }

    public function destroy(Customer $customer)
    {
        $customer->delete();
        return redirect()->route('master.customers.index')
            ->with('success', 'Customer deleted successfully');
    }
}