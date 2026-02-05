<?php

namespace App\Http\Controllers\Master;

use App\Models\Customer;
use App\Models\PaymentTerm;
use Illuminate\Http\Request;
use App\Helpers\StatusBadgeHelper;
use App\Http\Controllers\Controller;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Routing\Controllers\HasMiddleware;

class CustomerController extends Controller implements HasMiddleware
{
     public static function middleware(): array
    {
        return [
            new Middleware('permission:view-customers', only: ['index', 'show']),
            new Middleware('permission:create-customers', only: ['create', 'store']),
            new Middleware('permission:edit-customers', only: ['edit', 'update']),
            new Middleware('permission:delete-customers', only: ['destroy']),
        ];
    }

    public function index(Request $request)
    {
        $customers = Customer::with('paymentTerm')->paginate(15);

        if ($request->filled('search')) {
            $search = $request->search;
            $customers->where(function($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%");
            });
        }

        $columns = [
            ['key' => 'code', 'label' => 'Code', 'type' => 'text'],
            ['key' => 'name', 'label' => 'Name', 'type' => 'text'],
            ['key' => 'email', 'label' => 'Email', 'type' => 'text'],
            ['key' => 'phone', 'label' => 'Phone', 'type' => 'text'],
            ['key' => 'head_name', 'label' => 'Head', 'type' => 'text'],
            ['key'=> 'customer_type', 'label'=> 'Type', 'type'=> 'badge'],
            ['key' => 'status', 'label' => 'Status', 'type' => 'badge'],
        ];
        $customersData = $customers->map(function ($customer) {
            return [
                'id' => $customer->id,
                'code' => $customer->code,
                'name' => $customer->name,
                'email' => $customer->email ?? '-',
                'phone' => $customer->phone ?? '-',
                'head_name' => $customer->head_name ?? '-',
                'customer_type' => [
                    'value' => $customer->customer_type,
                    'label' => ucwords(str_replace('_', ' ', $customer->customer_type)),
                    'color' => match ($customer->customer_type) {
                        'hospital' => 'primary',
                        'clinic' => 'active',
                        'pharmacy' => 'warning',
                        'distributor' => 'success',
                        'retail' => 'info',
                        'government' => 'brand',
                        'other' => 'red',
                        default => 'red',
                    },
                ],
                'status' => [
                    'value' => $customer->status,
                    'label' => StatusBadgeHelper::getStatusLabel($customer->status),
                    'color' => StatusBadgeHelper::getStatusColor($customer->status),
                ],
                'actions' => [
                    'show' => route('master.customers.show', $customer),
                    'edit' => route('master.customers.edit', $customer),
                    'delete' => route('master.customers.destroy', $customer),
                ],
            ];
        })->toArray();


        return view('pages.master.customers.index', compact(
            'customers',
            'customersData',
            'columns'
        ));
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
