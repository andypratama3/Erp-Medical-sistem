<?php

namespace App\Http\Controllers\Master;

use App\Models\Employee;
use App\Models\MasterOffice;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\MasterDepartment;
use App\Http\Controllers\Controller;

class EmployeeController extends Controller
{
    public function index(Request $request)
    {
       $query = Employee::query()->with(['department', 'office']);

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('employee_code', 'like', "%{$search}%")
                  ->orWhere('employee_name', 'like', "%{$search}%")
                  ->orWhere('nik', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        // Filters
        if ($request->filled('dept_code')) {
            $query->where('dept_code', $request->dept_code);
        }

        if ($request->filled('level_type')) {
            $query->where('level_type', $request->level_type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $employees = $query->latest()->paginate(15);

        /* ============================
        TABLE COLUMNS (KEY BASED)
        ============================ */
        $columns = [
            ['key' => 'employee_code', 'label' => 'Code', 'type' => 'text'],
            ['key' => 'employee_name', 'label' => 'Name', 'type' => 'text'],
            ['key' => 'department', 'label' => 'Department', 'type' => 'text'],
            ['key' => 'office', 'label' => 'Office', 'type' => 'text'],
            ['key' => 'job_title', 'label' => 'Job Title', 'type' => 'text'],
            ['key' => 'level_type', 'label' => 'Level', 'type' => 'text'],
            ['key' => 'phone', 'label' => 'Phone', 'type' => 'text'],
            ['key' => 'email', 'label' => 'Email', 'type' => 'text'],
            ['key' => 'status', 'label' => 'Status', 'type' => 'badge'],
        ];

        /* ============================
        FORMAT DATA FOR TABLE
        ============================ */
        $employeesData = $employees->getCollection()->map(function ($employee) {
            return [
                'id' => $employee->id,
                'employee_code' => $employee->employee_code,
                'employee_name' => $employee->employee_name,
                'department' => $employee->department->name ?? '-',
                'office' => $employee->office->name ?? '-',
                'dept_code' => $employee->dept_code ?? '-',
                'level_type' => Employee::LEVEL_TYPES[$employee->level_type] ?? '-',
                'grade' => $employee->grade ?? '-',
                'job_title' => $employee->job_title ?? '-',
                'nik' => $employee->nik ?? '-',
                'office_code' => $employee->office_code ?? '-',
                'phone' => $employee->phone ?? '-',
                'email' => $employee->email ?? '-',
                'status' => [
                    'value' => $employee->status,
                    'label' => ucfirst($employee->status),
                    'color' => match ($employee->status) {
                        'active' => 'active',
                        'inactive' => 'inactive',
                        'resigned' => 'warning',
                        'terminated' => 'danger',
                        default => 'gray',
                    }
                ],

                'actions' => [
                    'show' => route('master.employees.show', $employee),
                    'edit' => route('master.employees.edit', $employee),
                    'delete' => route('master.employees.destroy', $employee),
                ],
            ];
        })->toArray();

        $departments = MasterDepartment::all();

        return view('pages.master.employees.index', compact('employees', 'columns', 'employeesData', 'departments'));
    }

    public function create()
    {
        $departments = MasterDepartment::all();
        $offices = MasterOffice::all();
        return view('pages.master.employees.create', compact('departments', 'offices'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_code' => 'required|string|max:255|unique:master_employees,employee_code',
            'employee_name' => 'required|string|max:255',
            'dept_code' => 'required|exists:master_departments,code',
            'office_code' => 'required|exists:master_offices,code',
            'job_title' => 'required|string|max:255',
            'level_type' => [
                'required',
                Rule::in(array_keys(\App\Models\Employee::LEVEL_TYPES)),
            ],
            'grade' => 'nullable|string|max:50',
            'payroll_status' => 'nullable|string|in:permanent,contract,probation',
            'payroll_level' => 'nullable|string|max:50',
            'nik' => 'required|string|max:16',
            'npwp' => 'nullable|string|max:20',
            'bpjs_tk_no' => 'nullable|string|max:50',
            'bpjs_kes_no' => 'nullable|string|max:50',
            'education' => 'nullable|string|in:SD,SMP,SMA/SMK,D3,S1,S2,S3',
            'join_year' => 'nullable|integer|min:1900|max:2100',
            'join_month' => 'nullable|integer|min:1|max:12',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'bank_name' => 'nullable|string|max:255',
            'bank_branch' => 'nullable|string|max:255',
            'bank_account_name' => 'nullable|string|max:255',
            'bank_account_number' => 'nullable|string|max:50',
            'status' => 'required|string|in:active,inactive,resigned,terminated',
            'note' => 'nullable|string',
        ]);

        Employee::create($validated);

        return redirect()->route('master.employees.index')
            ->with('success', 'Employee created successfully.');
    }

    public function show(Employee $employee)
    {
        $employee->load(['department', 'office']);
        return view('pages.master.employees.show', compact('employee'));
    }

    public function edit(Employee $employee)
    {
        $departments = MasterDepartment::all();
        $offices = MasterOffice::all();
        return view('pages.master.employees.edit', compact('employee', 'departments', 'offices'));
    }

    public function update(Request $request, Employee $employee)
    {
        $validated = $request->validate([
            'employee_code' => 'required|string|max:255|unique:master_employees,employee_code,' . $employee->id,
            'employee_name' => 'required|string|max:255',
            'dept_code' => 'required|exists:master_departments,code',
            'office_code' => 'required|exists:master_offices,code',
            'job_title' => 'required|string|max:255',
            'level_type' => [
                'required',
                Rule::in(array_keys(\App\Models\Employee::LEVEL_TYPES)),
            ],
            'grade' => 'nullable|string|max:50',
            'payroll_status' => 'nullable|string|in:permanent,contract,probation',
            'payroll_level' => 'nullable|string|max:50',
            'nik' => 'required|string|max:16',
            'npwp' => 'nullable|string|max:20',
            'bpjs_tk_no' => 'nullable|string|max:50',
            'bpjs_kes_no' => 'nullable|string|max:50',
            'education' => 'nullable|string|in:SD,SMP,SMA/SMK,D3,S1,S2,S3',
            'join_year' => 'nullable|integer|min:1900|max:2100',
            'join_month' => 'nullable|integer|min:1|max:12',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'bank_name' => 'nullable|string|max:255',
            'bank_branch' => 'nullable|string|max:255',
            'bank_account_name' => 'nullable|string|max:255',
            'bank_account_number' => 'nullable|string|max:50',
            'status' => 'required|string|in:active,inactive,resigned,terminated',
            'note' => 'nullable|string',
        ]);

        $employee->update($validated);

        return redirect()->route('master.employees.index')
            ->with('success', 'Employee updated successfully.');
    }

    public function destroy(Employee $employee)
    {
        try {
            $employee->delete();
            return redirect()->route('master.employees.index')
                ->with('success', 'Employee deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->route('master.employees.index')
                ->with('error', 'Failed to delete employee. It may be associated with other records.');
        }
    }
}