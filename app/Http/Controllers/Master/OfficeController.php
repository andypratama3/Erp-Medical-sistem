<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\MasterOffice;
use Illuminate\Http\Request;

class OfficeController extends Controller
{
    public function index()
    {
        $offices = MasterOffice::latest()->paginate(15);

        $columns = [
            ['key' => 'code', 'label' => 'Code', 'type' => 'text'],
            ['key' => 'name', 'label' => 'Name', 'type' => 'text'],
            ['key' => 'city', 'label' => 'City', 'type' => 'text'],
            ['key' => 'province', 'label' => 'Province', 'type' => 'text'],
            ['key' => 'phone', 'label' => 'Phone', 'type' => 'text'],
            ['key' => 'status', 'label' => 'Status', 'type' => 'badge'],
        ];

        $officesData = $offices->map(function ($office) {
            return [
                'id' => $office->id,
                'code' => $office->code,
                'name' => $office->name,
                'city' => $office->city ?? '-',
                'province' => $office->province ?? '-',
                'phone' => $office->phone ?? '-',
                'status' => [
                    'value' => $office->status,
                    'label' => ucfirst($office->status),
                    'color' => $office->status === 'active' ? 'green' : 'red',
                ],
                'actions' => [
                    'show' => route('master.offices.show', $office),
                    'edit' => route('master.offices.edit', $office),
                    'delete' => route('master.offices.destroy', $office),
                ],
            ];
        })->toArray();

        return view('pages.master.offices.index', compact(
            'offices',
            'officesData',
            'columns'
        ));
    }


    public function create()
    {

        return view('pages.master.offices.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|unique:master_offices|max:50',
            'name' => 'required|max:200',
            'address' => 'nullable',
            'city' => 'nullable|max:100',
            'province' => 'nullable|max:100',
            'postal_code' => 'nullable|max:10',
            'phone' => 'nullable|max:20',
            'email' => 'nullable|email|max:100',
            'status' => 'required|in:active,inactive',
        ]);

        MasterOffice::create($validated);

        return redirect()->route('master.offices.index')
            ->with('success', 'Office created successfully');
    }

    public function show(MasterOffice $office)
    {
        $office->load('departments');
        return view('pages.master.offices.show', compact('office'));
    }

    public function edit(MasterOffice $office)
    {
        return view('pages.master.offices.edit', compact('office'));
    }

    public function update(Request $request, MasterOffice $office)
    {
        $validated = $request->validate([
            'code' => 'required|max:50|unique:master_offices,code,' . $office->id,
            'name' => 'required|max:200',
            'address' => 'nullable',
            'city' => 'nullable|max:100',
            'province' => 'nullable|max:100',
            'postal_code' => 'nullable|max:10',
            'phone' => 'nullable|max:20',
            'email' => 'nullable|email|max:100',
            'status' => 'required|in:active,inactive',
        ]);

        $office->update($validated);

        return redirect()->route('master.offices.index')
            ->with('success', 'Office updated successfully');
    }

    public function destroy(MasterOffice $office)
    {
        $office->delete();
        return redirect()->route('master.offices.index')
            ->with('success', 'Office deleted successfully');
    }
}
