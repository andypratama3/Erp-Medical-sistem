<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\MasterDepartment;
use App\Models\MasterOffice;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    public function index()
    {
        $departments = MasterDepartment::with('office')->latest()->paginate(15);
        return view('pages.master.departments.index', compact('departments'));
    }

    public function create()
    {
        $offices = MasterOffice::active()->get();
        return view('pages.master.departments.create', compact('offices'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'office_id' => 'required|exists:master_offices,id',
            'code' => 'required|unique:master_departments|max:50',
            'name' => 'required|max:200',
            'head_name' => 'nullable|max:200',
            'phone' => 'nullable|max:20',
            'email' => 'nullable|email|max:100',
            'status' => 'required|in:active,inactive',
        ]);

        MasterDepartment::create($validated);

        return redirect()->route('master.departments.index')
            ->with('success', 'Department created successfully');
    }

    public function show(MasterDepartment $department)
    {
        $department->load('office');
        return view('pages.master.departments.show', compact('department'));
    }

    public function edit(MasterDepartment $department)
    {
        $offices = MasterOffice::active()->get();
        return view('pages.master.departments.edit', compact('department', 'offices'));
    }

    public function update(Request $request, MasterDepartment $department)
    {
        $validated = $request->validate([
            'office_id' => 'required|exists:master_offices,id',
            'code' => 'required|max:50|unique:master_departments,code,' . $department->id,
            'name' => 'required|max:200',
            'head_name' => 'nullable|max:200',
            'phone' => 'nullable|max:20',
            'email' => 'nullable|email|max:100',
            'status' => 'required|in:active,inactive',
        ]);

        $department->update($validated);

        return redirect()->route('master.departments.index')
            ->with('success', 'Department updated successfully');
    }

    public function destroy(MasterDepartment $department)
    {
        $department->delete();
        return redirect()->route('master.departments.index')
            ->with('success', 'Department deleted successfully');
    }
}