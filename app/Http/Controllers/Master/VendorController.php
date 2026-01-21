<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Vendor;
use App\Models\PaymentTerm;
use Illuminate\Http\Request;

class VendorController extends Controller
{
    public function index()
    {
        $vendors = Vendor::with('paymentTerm')->latest()->paginate(15);

        $columns = [
            ['key' => 'code', 'label' => 'Code', 'type' => 'text'],
            ['key' => 'name', 'label' => 'Name', 'type' => 'text'],
            ['key' => 'email', 'label' => 'Email', 'type' => 'text'],
            ['key' => 'phone', 'label' => 'Phone', 'type' => 'text'],
            ['key' => 'payment_term', 'label' => 'Payment Term', 'type' => 'text'],
            ['key' => 'status', 'label' => 'Status', 'type' => 'badge'],
        ];

        $vendorsData = $vendors->getCollection()->map(function ($vendor) {
            return [
                'id' => $vendor->id,
                'code' => $vendor->code,
                'name' => $vendor->name,
                'email' => $vendor->email ?? '-',
                'phone' => $vendor->phone ?? '-',
                'payment_term' => $vendor->paymentTerm?->name ?? '-',
                'status' => [
                    'value' => $vendor->status,
                    'label' => ucfirst($vendor->status),
                    'color' => match ($vendor->status) {
                        'active' => 'green',
                        'inactive' => 'red',
                        'blocked' => 'yellow',
                        default => 'gray',
                    }
                ],
                'actions' => [
                    'show' => route('master.vendors.show', $vendor),
                    'edit' => route('master.vendors.edit', $vendor),
                    'delete' => route('master.vendors.destroy', $vendor),
                ],
            ];
        })->toArray();

        return view('pages.master.vendors.index', compact('vendors', 'vendorsData', 'columns'));
    }

    public function create()
    {
        $paymentTerms = PaymentTerm::active()->get();
        return view('pages.master.vendors.create', compact('paymentTerms'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|unique:master_vendors|max:50',
            'name' => 'required|max:255',
            'legal_name' => 'nullable|max:255',
            'npwp' => 'nullable|max:50',
            'address' => 'nullable',
            'city' => 'nullable|max:100',
            'province' => 'nullable|max:100',
            'postal_code' => 'nullable|max:10',
            'phone' => 'nullable|max:20',
            'email' => 'nullable|email|max:100',
            'contact_person' => 'nullable|max:200',
            'contact_phone' => 'nullable|max:20',
            'payment_term_id' => 'nullable|exists:master_payment_terms,id',
            'vendor_type' => 'required|in:manufacturer,distributor,supplier,service_provider,other',
            'status' => 'required|in:active,inactive',
            'notes' => 'nullable',
        ]);

        Vendor::create($validated);

        return redirect()->route('master.vendors.index')
            ->with('success', 'Vendor created successfully');
    }

    public function show(Vendor $vendor)
    {
        $vendor->load('paymentTerm');
        return view('pages.master.vendors.show', compact('vendor'));
    }

    public function edit(Vendor $vendor)
    {
        $paymentTerms = PaymentTerm::active()->get();
        return view('pages.master.vendors.edit', compact('vendor', 'paymentTerms'));
    }

    public function update(Request $request, Vendor $vendor)
    {
        $validated = $request->validate([
            'code' => 'required|max:50|unique:master_vendors,code,' . $vendor->id,
            'name' => 'required|max:255',
            'legal_name' => 'nullable|max:255',
            'npwp' => 'nullable|max:50',
            'address' => 'nullable',
            'city' => 'nullable|max:100',
            'province' => 'nullable|max:100',
            'postal_code' => 'nullable|max:10',
            'phone' => 'nullable|max:20',
            'email' => 'nullable|email|max:100',
            'contact_person' => 'nullable|max:200',
            'contact_phone' => 'nullable|max:20',
            'payment_term_id' => 'nullable|exists:master_payment_terms,id',
            'vendor_type' => 'required',
            'status' => 'required|in:active,inactive',
            'notes' => 'nullable',
        ]);

        $vendor->update($validated);

        return redirect()->route('master.vendors.index')
            ->with('success', 'Vendor updated successfully');
    }

    public function destroy(Vendor $vendor)
    {
        $vendor->delete();
        return redirect()->route('master.vendors.index')
            ->with('success', 'Vendor deleted successfully');
    }
}
