<?php

namespace App\Http\Controllers\Master;

use App\Models\Employee;
use Illuminate\Http\Request;
use App\Models\MasterDepartment;
use App\Http\Controllers\Controller;

class EmployeeController extends Controller
{
    public function index(Request $request)
    {
       $query = Employee::query();

       if ($request->search) {
           $query->where('name', 'like', "%{$request->search}%");
       }

       // Filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('', 'like', "%{$search}%")
                ->orWhere('name', 'like', "%{$search}%");
            });
        }

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
            ['key' => 'name', 'label' => 'Name', 'type' => 'text'],
            ['key' => 'phone', 'label' => 'Phone', 'type' => 'text'],
            ['key' => 'email', 'label' => 'Email', 'type' => 'text'],
            ['key' => 'employee_code', 'label' => 'Code', 'type' => 'text'],
            ['key' => 'dept_code', 'label' => 'Department', 'type' => 'text'],
            ['key' => 'level_type', 'label' => 'Level', 'type' => 'text'],
            ['key' => 'grade', 'label' => 'Grade', 'type' => 'text'],
            ['key' => 'job_title', 'label' => 'Job Title', 'type' => 'text'],
            ['key' => 'nik', 'label' => 'NIK', 'type' => 'text'],
            ['key' => 'office_code', 'label' => 'Office Code', 'type' => 'text'],
            ['key' => 'join_year', 'label' => 'Join Year', 'type' => 'text'],
            ['key' => 'join_month', 'label' => 'Join Month', 'type' => 'text'],
            ['key' => 'status', 'label' => 'Status', 'type' => 'badge'],
          
          
        ];

        /* ============================
        FORMAT DATA FOR TABLE
        ============================ */
        $employeesData = $employees->getCollection()->map(function ($employee) {
            return [
                'id' => $employee->id,
                'employee_code' => $employee->employee_code,
                'name' => $employee->name,
                'dept_code' => $employee->dept_code?->name ?? '-',
                'level_type' => $employee->level_type?->name ?? '-',
                'grade' => $employee->grade?->name ?? '-',
                'job_title' => $employee->job_title?->name ?? '-',
                'nik' => $employee->nik?->name ?? '-',
                'office_code' => $employee->office_code?->name ?? '-',
                'join_year' => $employee->join_year?->name ?? '-',
                'join_month' => $employee->join_month?->name ?? '-',
                'phone' => $employee->phone?->name ?? '-',
                'email' => $employee->email?->name ?? '-',
                'status' => [
                    'value' => $employee->status,
                    'label' => ucfirst($employee->status),
                    'color' => match ($employee->status) {
                        'active' => 'active',
                        'inactive' => 'inactive',
                        'discontinued' => 'warning',
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
        return view('pages.master.employees.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required',
            'phone' => 'required',
            'address' => 'required',
            'city' => 'required',
            'state' => 'required',
            'zip' => 'required',
            'country' => 'required',
        ]);

        Employee::create($request->all());

        return redirect()->route('master.employees.index')
            ->with('success', 'Employee created successfully.');
    }

    public function edit(Employee $employee)
    {
        return view('pages.master.employees.edit', compact('employee'));
    }

    public function update(Request $request, Employee $employee)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required',
            'phone' => 'required',
            'address' => 'required',
            'city' => 'required',
            'state' => 'required',
            'zip' => 'required',
            'country' => 'required',
        ]);

        $employee->update($request->all());

        return redirect()->route('master.employees.index')
            ->with('success', 'Employee updated successfully.');
    }

    public function destroy(Employee $employee)
    {
        $employee->delete();

        return redirect()->route('master.employees.index')
            ->with('success', 'Employee deleted successfully.');
    }
}
