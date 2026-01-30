<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Tax;
use Illuminate\Http\Request;

class TaxController extends Controller
{
    public function index(Request $request)
    {
        $query = Tax::query();

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

        $taxes = $query->latest()->paginate(15);

        /* ============================
        TABLE COLUMNS (KEY BASED)
        ============================ */
        $columns = [
            ['key' => 'code', 'label' => 'Code', 'type' => 'text'],
            ['key' => 'name', 'label' => 'Name', 'type' => 'text'],
            ['key' => 'rate', 'label' => 'Rate (%)', 'type' => 'text'],
            ['key' => 'description', 'label' => 'Description', 'type' => 'text'],
            ['key' => 'status', 'label' => 'Status', 'type' => 'badge'],
        ];

        /* ============================
        FORMAT DATA FOR TABLE
        ============================ */
        $taxesData = $taxes->getCollection()->map(function ($tax) {
            return [
                'id' => $tax->id,
                'code' => $tax->code,
                'name' => $tax->name,
                'rate' => $tax->rate . '%',
                'description' => $tax->description ?? '-',

                'status' => [
                    'value' => $tax->status,
                    'label' => ucfirst($tax->status),
                    'color' => match ($tax->status) {
                        'active' => 'active',
                        'inactive' => 'inactive',
                        default => 'gray',
                    }
                ],

                'actions' => [
                    'show' => route('master.taxes.show', $tax),
                    'edit' => route('master.taxes.edit', $tax),
                    'delete' => route('master.taxes.destroy', $tax),
                ],
            ];
        })->toArray();

        return view('pages.master.taxes.index', compact(
            'columns',
            'taxes',
            'taxesData'
        ));
    }

    public function create()
    {
        return view('pages.master.taxes.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:master_tax,code',
            'name' => 'required|string|max:200',
            'rate' => 'required|numeric|min:0|max:100',
            'description' => 'nullable|string',
            'status' => 'required|in:active,inactive',
        ]);

        Tax::create($validated);

        return redirect()->route('master.taxes.index')
            ->with('success', 'Tax created successfully.');
    }

    public function show(Tax $tax)
    {
        return view('pages.master.taxes.show', compact('tax'));
    }

    public function edit(Tax $tax)
    {
        return view('pages.master.taxes.edit', compact('tax'));
    }

    public function update(Request $request, Tax $tax)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:master_tax,code,' . $tax->id,
            'name' => 'required|string|max:200',
            'rate' => 'required|numeric|min:0|max:100',
            'description' => 'nullable|string',
            'status' => 'required|in:active,inactive',
        ]);

        $tax->update($validated);

        return redirect()->route('master.taxes.index')
            ->with('success', 'Tax updated successfully.');
    }

    public function destroy(Tax $tax)
    {
        $tax->delete();

        return redirect()->route('master.taxes.index')
            ->with('success', 'Tax deleted successfully.');
    }
}
