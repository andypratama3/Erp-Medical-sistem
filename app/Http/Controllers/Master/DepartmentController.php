<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\MasterDepartment;
use App\Models\MasterOffice;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    public function index(Request $request)
    {
        $query = MasterDepartment::with('office');

        // Filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%")
                  ->orWhere('head_name', 'like', "%{$search}%");
            });
        }

        if ($request->filled('office_id')) {
            $query->where('office_id', $request->office_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $departments = $query->latest()->paginate(15);

        /* ============================
        TABLE COLUMNS (KEY BASED)
        ============================ */
        $columns = [
            ['key' => 'code', 'label' => 'Code', 'type' => 'text'],
            ['key' => 'name', 'label' => 'Name', 'type' => 'text'],
            ['key' => 'office', 'label' => 'Office', 'type' => 'text'],
            ['key' => 'head_name', 'label' => 'Head', 'type' => 'text'],
            ['key' => 'status', 'label' => 'Status', 'type' => 'badge'],
        ];

        /* ============================
        FORMAT DATA FOR TABLE
        ============================ */
        $departmentsData = $departments->getCollection()->map(function ($department) {
            return [
                'id' => $department->id,
                'code' => $department->code,
                'name' => $department->name,
                'office' => $department->office?->name ?? '-',
                'head_name' => $department->head_name ?? '-',

                'status' => [
                    'value' => $department->status,
                    'label' => ucfirst($department->status),
                    'color' => $department->status ,
                ],

                'actions' => [
                    'show' => route('master.departments.show', $department),
                    'edit' => route('master.departments.edit', $department),
                    'delete' => route('master.departments.destroy', $department),
                ],
            ];
        })->toArray();

        $offices = MasterOffice::active()->get();

        return view('pages.master.departments.index', compact(
            'columns',
            'departments',
            'departmentsData',
            'offices'
        ));
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
        $offices = MasterOffice::active()->get();
        return view('pages.master.departments.show', compact('department','offices'));
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
        try {
            $department->delete();
            return redirect()->route('master.departments.index')
                ->with('success', 'Department deleted successfully');
        } catch (\Exception $e) {
            return redirect()->route('master.departments.index')
                ->with('error', 'Failed to delete department. It may be in use.');
        }
    }
}
