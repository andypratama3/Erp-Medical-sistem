<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Manufacture;
use Illuminate\Http\Request;

class ManufactureController extends Controller
{
    public function index()
    {
        $manufactures = Manufacture::latest()->paginate(15);

        $columns = [
            ['key' => 'code', 'label' => 'Code', 'type' => 'text'],
            ['key' => 'name', 'label' => 'Name', 'type' => 'text'],
            ['key' => 'country', 'label' => 'Country', 'type' => 'text'],
            ['key' => 'city', 'label' => 'City', 'type' => 'text'],
            ['key' => 'phone', 'label' => 'Phone', 'type' => 'text'],
            ['key' => 'status', 'label' => 'Status', 'type' => 'badge'],
        ];

        $manufacturesData = $manufactures->getCollection()->map(function ($manufacture) {
            return [
                'id' => $manufacture->id,
                'code' => $manufacture->code,
                'name' => $manufacture->name,
                'country' => $manufacture->country ?? '-',
                'city' => $manufacture->city ?? '-',
                'phone' => $manufacture->phone ?? '-',
                'status' => [
                    'value' => $manufacture->status,
                    'label' => match($manufacture->status) {
                        'active' => 'Active',
                        'inactive' => 'Inactive',
                    },
                    'color' => $manufacture->status,
                ],
                'actions' => [
                    'show' => route('master.manufactures.show', $manufacture),
                    'edit' => route('master.manufactures.edit', $manufacture),
                    'delete' => route('master.manufactures.destroy', $manufacture),
                ],
            ];
        })->toArray();

        return view('pages.master.manufactures.index', compact('manufactures','columns', 'manufacturesData'));
    }

    public function create()
    {
        return view('pages.master.manufactures.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|unique:master_manufactures|max:50',
            'name' => 'required|max:200',
            'country' => 'nullable|max:100',
            'address' => 'nullable',
            'city' => 'nullable|max:100',
            'phone' => 'nullable|max:20',
            'email' => 'nullable|email|max:100',
            'website' => 'nullable|url|max:255',
            'description' => 'nullable',
            'status' => 'required|in:active,inactive',
        ]);

        Manufacture::create($validated);

        return redirect()->route('master.manufactures.index')
            ->with('success', 'Manufacture created successfully');
    }

    public function show(Manufacture $manufacture)
    {
        $manufacture->load(['products', 'documents', 'regAlkesCases']);
        return view('pages.master.manufactures.show', compact('manufacture'));
    }

    public function edit(Manufacture $manufacture)
    {
        return view('pages.master.manufactures.edit', compact('manufacture'));
    }

    public function update(Request $request, Manufacture $manufacture)
    {
        $validated = $request->validate([
            'code' => 'required|max:50|unique:master_manufactures,code,' . $manufacture->id,
            'name' => 'required|max:200',
            'country' => 'nullable|max:100',
            'address' => 'nullable',
            'city' => 'nullable|max:100',
            'phone' => 'nullable|max:20',
            'email' => 'nullable|email|max:100',
            'website' => 'nullable|url|max:255',
            'description' => 'nullable',
            'status' => 'required|in:active,inactive',
        ]);

        $manufacture->update($validated);

        return redirect()->route('master.manufactures.index')
            ->with('success', 'Manufacture updated successfully');
    }

    public function destroy(Manufacture $manufacture)
    {
        $manufacture->delete();
        return redirect()->route('master.manufactures.index')
            ->with('success', 'Manufacture deleted successfully');
    }
}
